<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Helpers/NumericHelper.php";
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

        $tableNameWithFistUppercaseLetter = ucfirst($requestData["table"]);
        $databaseRequest = "SELECT * FROM `" . $tableNameWithFistUppercaseLetter . "`";

        if (isset($requestData["id"]) && $requestData["id"]) {
            $id = NumericHelper\getInt($requestData["id"]);

            if (!$id) {
                return [
                    "status" => "error",
                    "message" => "Некорректное значение id"
                ];
            }

            $databaseRequest = $databaseRequest . " WHERE id = '" . $id . "' LIMIT 1";
        }

        global $DB;
        $neededData = $DB->query($databaseRequest);
        $neededData = $neededData->fetchAll(PDO::FETCH_ASSOC);

        return [
            "status" => "ok",
            "payload" => $neededData
        ];
    }
}
