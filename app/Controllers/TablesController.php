<?php

class TablesController {
    public function getTable($requestData) {
        if (!isset($requestData["table"]) || !$requestData["table"]) {
            return [
                "status" => "error",
                "message" => "Название таблицы не указано"
            ];
        }

        $allowedTables = [
            "news",
            "session"
        ];

        if (!in_array($requestData["table"], $allowedTables)) {
            return [
                "status" => "error",
                "message" => "Неверное название таблицы"
            ];
        }

        $tableNameWithFistUppercaseLetter = ucfirst($requestData["table"]);
        $databaseRequest = "SELECT * FROM " . $tableNameWithFistUppercaseLetter;

        if (isset($requestData["id"]) && $requestData["id"]) {
            $id = getInt($requestData["id"]);
            $databaseRequest += "WHERE id = " . $id . " LIMIT 1";
        }


        // return $databaseRequest;

        $neededData = $DB->prepare($databaseRequest);

        return 'hi';

        $neededData = $neededData->execute();
        $neededData = $neededData->fetchAll(PDO::FETCH_ASSOC);
        
    }

    private function getInt($val) {
        if (is_numeric($val)) {
          return (int) round($val + 0);
        }
        return 0;
      }
}