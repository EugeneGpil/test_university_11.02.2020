<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Helpers/NumericHelper.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/News.php";

use App\Helpers\NumericHelper;

class TablesController
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

        $tableClassName = "\\App\\Models\\" . ucfirst($requestData["table"]);
        $neededTable = new $tableClassName();

        if (isset($requestData["id"])) {
            $id = NumericHelper\getInt($requestData["id"]);

            if (!$id) {
                return [
                    "status" => "error",
                    "message" => "Некорректное значение id"
                ];
            }

            return [
                "starus" => "ok",
                "payload" => $neededTable->getById($id)
            ];
        }

        return [
            "status" => "ok",
            "payload" => $neededTable->getAll()
        ];
    }
}
