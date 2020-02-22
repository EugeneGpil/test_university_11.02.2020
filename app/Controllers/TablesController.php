<?php

namespace App\Controllers;

use App\Controllers\Base\Controller;
use App\Response;

class TablesController extends Controller
{
    private const ALLOWED_TABLES = [
        "news",
        "session"
    ];

    public function getTable($requestData)
    {
        if (!isset($requestData["table"]) || !$requestData["table"]) {
            return Response::response(false, "Название таблицы не указано");
        }

        if (!in_array($requestData["table"], self::ALLOWED_TABLES)) {
            return Response::response(false, "Неверное название таблицы");
        }

        $tableController = "\\App\\Controllers\\Tables\\" . ucfirst($requestData["table"]) . "Controller";
        $neededController = new $tableController();
        return $neededController->getTable($requestData);
    }
}
