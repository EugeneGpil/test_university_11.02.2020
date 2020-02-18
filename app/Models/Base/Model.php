<?php

namespace App\Models\Base;

use PDO;

abstract class Model
{
    protected $tabelName;
    protected static $DB = null;

    public static function getDB() {

        if (self::$DB) {
            return self::$DB;
        }

        $config = include(__DIR__ . "/../../../config.php");
        self::$DB = new PDO(
            "mysql:host=" . $config["database_server"] .
                ";dbname=" . $config["database_name"],
            $config["database_user"],
            $config["database_password"],
            [
                PDO::ATTR_TIMEOUT => $config["database_connection_timeout"],
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return self::$DB;
    }

    public function getAll() : array
    {
        $all = $this->getDB()->query("SELECT * FROM `" . $this->tableName . "`");
        return $this->changeListKeys($all->fetchAll());
    }

    public function getById($id) : array
    {
        return $this->getByColumn("id", $id);
    }

    public function getByColumn($columnName, $value)
    {
        $needed = $this->getDB()->prepare("
            SELECT * FROM `" . $this->tableName . "` WHERE `" . $columnName . "` = ? LIMIT 1
        ");
        $needed->execute([$value]);
        $needed = $needed->fetchAll();
        if (empty($needed)) { return []; }
        return $this->changeArrayKeys($needed[0]);
    }

    protected function addRelation($relationshipTableName, $id1, $id2) : bool
    {
        $model = new Model();
        [$firstTableName, $secondTableName] = $model->getRelatedTableNames($relationshipTableName);

        $request = $this->getDB()->prepare("
            INSERT INTO `" . $relationshipTableName . "` (`" . $firstTableName . "`, `" . $secondTableName . "`)
            VALUES (?, ?)
        ");
        $request->execute([$id1, $id2]);
        return true;
    }

    protected function isItRelated($relationsTableName, $id1, $id2) : bool
    {
        $model = new Model();
        [$firstTableName, $secondTableName] = $model->getRelatedTableNames($relationsTableName);

        $result = $this->getDB()->prepare("
            SELECT COUNT(*)
            FROM `" . $relationsTableName . "`
            WHERE `" . $firstTableName . "` = ? AND `" . $secondTableName . "` = ?;
        ");
        $result->execute([$id1, $id2]);
        $result = $result->fetchAll()[0];

        return (bool) $result["COUNT(*)"];
    }

    protected function getByIdWithRelations($id, $relationshipTableName) : array
    {

        $mainData = $this->getById($id);

        if (empty($mainData)) { return []; }

        $toRelateTableName = $this->getToRelateTableName($relationshipTableName);

        $relations = $this->getDB()->prepare("
            SELECT `" . $toRelateTableName . "`.`id`, `" . $toRelateTableName . "`.`name`
                FROM `" . $relationshipTableName . "` LEFT JOIN `$toRelateTableName`
                ON `" . $toRelateTableName . "` = `" . $toRelateTableName . "`.`id`
                WHERE " . $relationshipTableName . "." . $this->tableName . " = ?;
        ");
        $relations->execute([$id]);
        $relations = $relations->fetchAll();

        $mainData[ucfirst($toRelateTableName . "s")] = $this->changeListKeys($relations);
        return $mainData;
    }

    protected function getAllWithRelations($relationshipTableName) : array
    {
        $mainData = $this->getAll();

        $secondTableName = $this->getToRelateTableName($relationshipTableName);

        $relations = $this->getDB()->query("
            SELECT `" . $this->tableName . "`, `" . $secondTableName . "`
            FROM `" . $relationshipTableName . "`;
        ");
        $relations = $relations->fetchAll();

        $secondTable = $this->getDB()->query("
            SELECT * FROM `" . $secondTableName . "`;
        ");
        $secondTable = $secondTable->fetchAll();
        
        foreach ($mainData as &$mainElement) {
            $allRelations = $this->getAllRelations(
                $relations, $relationshipTableName, $secondTableName, $mainElement["ID"]
            );
            $mainElement[ucfirst($secondTableName . "s")] =
                $this->changeListKeys($this->getByIds($secondTable, $allRelations));
        }

        return $mainData;
    }

    private function getRelatedTableNames($relationshipTableName) : array
    {
        $underscorePosition = strpos($relationshipTableName, '_');
        $firstTableName = substr($relationshipTableName, 0, $underscorePosition);
        $secondTableName = substr($relationshipTableName, $underscorePosition + 1);
        return [$firstTableName, $secondTableName];
    }

    private function getByIds($table, $ids) : array
    {
        $result = [];
        foreach ($table as $element) {
            if (in_array($element["id"], $ids)) {
                $result[] = $element;
            }
        }
        return $result;
    }

    private function getAllRelations($relations, $relationshipTableName, $secondTableName, $id) : array
    {
        $relationsIds = [];

        foreach ($relations as $relation) {
            if ($relation[$this->tableName] == $id) {
                $relationsIds[] = $relation[$secondTableName];
            }
        }

        return $relationsIds;
    }

    private function changeListKeys($array) : array
    {
        foreach ($array as &$element) {
            $element = $this->changeArrayKeys($element);
        }
        return $array;
    }

    private function changeArrayKeys($array) : array
    {
        foreach ($array as $key => $element) {
            $this->changeKey($array, $key, $element);
        }
        return $array;
    }

    private function changeKey(&$element, $key, $value) : void
    {
        $element[$this->getChangedKey($key)] = $value;
        unset($element[$key]);
    }

    private function getToRelateTableName($relationshipTableName) : string
    {
        $underscorePosition = strpos($relationshipTableName, '_');
        $firstTableName = substr($relationshipTableName, 0, $underscorePosition);
        if ($firstTableName != $this->tableName) {
            return $firstTableName;
        }
        return substr($relationshipTableName, $underscorePosition + 1);
    }

    private function getChangedKey($string) : string
    {
        if ($string == "id") {
            return "ID";
        }
        return str_replace('_', '', ucwords($string, '_'));
    }
}
