<?php

namespace App\Models\Base;

use PDO;

class Model
{
    protected $tabelName;

    public function getAll()
    {
        global $DB;
        $all = $DB->query("SELECT * FROM `" . $this->tableName . "`");
        return $all->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        global $DB;
        $needed = $DB->prepare("SELECT * FROM `" . $this->tableName . "` WHERE `ID` = ? LIMIT 1");
        $needed->execute([$id]);
        return $needed->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getByIdWithRelations($id, $relationshipTableName)
    {

        $mainData = $this->getById($id);

        $toRelateTableName = $this->getToRelateTableName($relationshipTableName);

        global $DB;

        $relations = $DB->prepare("
            SELECT `" . $toRelateTableName . "`.`id`, `" . $toRelateTableName . "`.`name`
                FROM `" . $relationshipTableName . "` LEFT JOIN `$toRelateTableName`
                ON `" . $toRelateTableName . "` = `" . $toRelateTableName . "`.`id`
                WHERE " . $relationshipTableName . "." . $toRelateTableName . " = ?;
        ");
        $relations->execute([$id]);
        $relations = $relations->fetchAll(PDO::FETCH_ASSOC);

        $mainData["speakers"] = $relations;
        return $mainData;
    }

    private function getToRelateTableName($relationshipTableName)
    {
        $underscorePosition = strpos($relationshipTableName, '_');
        $firstTableName = substr($relationshipTableName, 0, $underscorePosition);
        if ($firstTableName != $this->tableName) {
            return $firstTableName;
        }
        return substr($relationshipTableName, $underscorePosition + 1);
    }
}
