<?php

namespace App\Models\Base;

use PDO;

class Model
{
    protected $tabelName;
    protected $columnNames;

    public function getAll() : array
    {
        global $DB;
        $all = $DB->query("SELECT * FROM `" . $this->tableName . "`");
        return $this->changeKeys($all->fetchAll());
    }

    public function getById($id) : array
    {
        global $DB;
        $needed = $DB->prepare("SELECT * FROM `" . $this->tableName . "` WHERE `ID` = ? LIMIT 1");
        $needed->execute([$id]);

        return $this->changeKeys($needed->fetchAll());;
    }

    protected function getByIdWithRelations($id, $relationshipTableName) : array
    {

        $mainData = $this->getById($id);

        $toRelateTableName = $this->getToRelateTableName($relationshipTableName);

        global $DB;
        $relations = $DB->prepare("
            SELECT `" . $toRelateTableName . "`.`id`, `" . $toRelateTableName . "`.`name`
                FROM `" . $relationshipTableName . "` LEFT JOIN `$toRelateTableName`
                ON `" . $toRelateTableName . "` = `" . $toRelateTableName . "`.`id`
                WHERE " . $relationshipTableName . "." . $this->tableName . " = ?;
        ");
        $relations->execute([$id]);
        $relations = $relations->fetchAll();

        $mainData[0][ucfirst($toRelateTableName . "s")] = $this->changeKeys($relations);
        return $mainData;
    }

    protected function getAllWithRelations($relationshipTableName)
    {
        $mainData = $this->getAll();

        global $DB;
        $relations = $DB->query("
            SELECT `" . $this->tableName . "`, `" . $this->getToRelateTableName($relationshipTableName) . "`
            FROM `" . $relationshipTableName . "`;
        ");
        $relations = $relations->fetchAll();

        $secondTable = $DB->query("
            SELECT * FROM `" . $this->getToRelateTableName($relationshipTableName) . "`;
        ");
        $secondTable = $secondTable->fetchAll();

        /////////////////

        return $secondTable;
    }

    private function changeKeys($array) : array
    {
        foreach ($array as &$element) {
            $element = $this->changeArrayKeysToPascalCase($element);
        }
        return $array;
    }

    private function changeArrayKeysToPascalCase($array) : array
    {
        foreach ($array as $key => $element) {
            $this->changeKeyToPascalCase($array, $key, $element);
        }
        return $array;
    }

    private function changeKeyToPascalCase(&$element, $key, $value) : void
    {
        $element[$this->toPascalCase($key)] = $value;
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

    private function toPascalCase($string) : string
    {
        if ($string == "id") {
            return "ID";
        }
        return str_replace('_', '', ucwords($string, '_'));
    }
}
