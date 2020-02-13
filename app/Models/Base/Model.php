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
}
