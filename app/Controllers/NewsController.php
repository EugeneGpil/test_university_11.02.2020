<?php

namespace App\Controllers;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/News.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Helpers/NumericHelper.php";

use App\Models\News;
use App\Helpers\NumericHelper;

class NewsController
{
    public function getTable($request)
    {
        $news = new News();

        if (!isset($request["id"])) {
            return [
                "stutus" => "ok",
                "payload" => $news->getAll()
            ];
        }

        $id = NumericHelper\getInt($request["id"]);

        if (!$id) {
            return [
                "status" => "error",
                "message" => "Некорректное значение id"
            ];
        }

        return [
            "status" => "ok",
            "payload" => $news->getById($id)
        ];
    }
}