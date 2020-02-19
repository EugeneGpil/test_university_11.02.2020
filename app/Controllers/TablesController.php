<?php

namespace App\Controllers;

use App\Controllers\Base\Controller;

class TablesController extends Controller
{
    private const ALLOWED_TABLES = [
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

        if (!in_array($requestData["table"], self::ALLOWED_TABLES)) {
            return [
                "status" => "error",
                "message" => "Неверное название таблицы"
            ];
        }

        $tableController = "\\App\\Controllers\\Tables\\" . ucfirst($requestData["table"]) . "Controller";
        $neededController = new $tableController();
        return $neededController->getTable($requestData);
    }
}
