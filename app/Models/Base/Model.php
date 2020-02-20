<?php

namespace App\Models\Base;

use PDO;

abstract class Model
{
    protected $tabelName;
    protected $columnNames;
    protected $relationTableColumnNames;
    protected static $DB = null;

    private const TABLE_NAMES = [
        "news",
        "participant",
        "session",
        "speaker",
        "session_participant",
        "session_speaker"
    ];

    private const CONNECTION_PARAMS = [
        "default",
        "no_database"
    ];

    public static function getDB($param = "default") {

        if (!in_array($param, self::CONNECTION_PARAMS)) {
            return null;
        }

        if (self::$DB[$param]) {
            return self::$DB[$param];
        }
        
        $config = include(__DIR__ . "/../../../config.php");
        $hostDatabaseString = "mysql:host=" . $config["database_server"];
        if ($param != "no_database") {
            $hostDatabaseString = $hostDatabaseString . ";dbname=" . $config["databese_name"];
        }

        self::$DB[$param] = new PDO(
            $hostDatabaseString,
            $config["database_user"],
            $config["database_password"],
            [
                PDO::ATTR_TIMEOUT => $config["database_connection_timeout"],
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return self::$DB[$param];
    }

    public function getAll() : array
    {
        if (!in_array($this->tableName, self::TABLE_NAMES)) { return []; }
        $all = $this->getDB()->query("SELECT * FROM `" . $this->tableName . "`");
        return $this->changeListKeys($all->fetchAll());
    }

    public function getById($id) : array
    {
        return $this->getByColumn("id", $id);
    }

    public function getByColumn($columnName, $value)
    {
        if (!in_array($this->tableName, self::TABLE_NAMES)
            || !in_array($columnName, $this->columnNames)) { return []; }
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
        [$firstTableName, $secondTableName] = self::getRelatedTableNames($relationshipTableName);

        if (!$this->isRelationTableNamesAllowed($relationshipTableName, $firstTableName, $secondTableName)) {
            return [];
        }

        $request = $this->getDB()->prepare("
            INSERT INTO `" . $relationshipTableName . "` (`" . $firstTableName . "`, `" . $secondTableName . "`)
            VALUES (?, ?)
        ");
        $request->execute([$id1, $id2]);
        return true;
    }

    protected function isRelationTableNamesAllowed($relationshipTableName, $firstTableName, $secondTableName)
    {
        if (!in_array($relationshipTableName, self::TABLE_NAMES)
            || !isset($this->relationTableColumnNames[$relationshipTableName])
            || !in_array($firstTableName, $this->relationTableColumnNames[$relationshipTableName])
            || !in_array($secondTableName, $this->relationTableColumnNames[$relationshipTableName]))
                { return false; }

        return true;
    }

    protected function isItRelated($relationsTableName, $id1, $id2) : bool
    {
        [$firstTableName, $secondTableName] = self::getRelatedTableNames($relationsTableName);

        if (!$this->isRelationTableNamesAllowed($relationsTableName, $firstTableName, $secondTable)) {
            return [];
        }

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

        if (!in_array($toRelateTableName, self::TABLE_NAMES)
            || !in_array($relationshipTableName, self::TABLE_NAMES)
            || !in_array($this->tableName, self::TABLE_NAMES)
            || !isset($this->relationTableColumnNames[$relationshipTableName])
            || !in_array($toRelateTableName, $this->relationTableColumnNames[$relationshipTableName]))
                { return []; }

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

        if (!$this->isRelationTableNamesAllowed($relationshipTableName, $this->tableName, $secondTableName)) {
            return [];
        }

        $relations = $this->getDB()->query("
            SELECT `" . $this->tableName . "`, `" . $secondTableName . "`
            FROM `" . $relationshipTableName . "`;
        ");
        $relations = $relations->fetchAll();

        if (!in_array($secondTableName, self::TABLE_NAMES)) {
            return [];
        }

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

    protected static function getRelatedTableNames($relationshipTableName) : array
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
