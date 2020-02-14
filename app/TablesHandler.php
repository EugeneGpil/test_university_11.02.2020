<?php

namespace App;

class TablesHandler
{
    private $allowedTables = [
        "news",
        "session"
    ];

    public function getTable($requestData)
    {
        if (!isset($requestData["table"]) || !$requestData["table"]) {
            return [
                "status" => "error",
                "message" => "Название таблицы не указано"
            ];
        }

        if (!in_array($requestData["table"], $this->allowedTables)) {
            return [
                "status" => "error",
                "message" => "Неверное название таблицы"
            ];
        }

        require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Controllers/" . ucfirst($requestData["table"]) . "Controller.php";

        $tableController = "\\App\\Controllers\\" . ucfirst($requestData["table"]) . "Controller";
        $neededController = new $tableController();
        return $neededController->getTable($requestData);
    }
}
