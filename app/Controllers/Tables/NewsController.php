<?php

namespace App\Controllers\Tables;

use App\Models\News;
use App\Helpers\NumericHelper;

class NewsController
{
    public function getTable($request)
    {
        $news = new News();

        if (!isset($request["id"])) {
            return [
                "status" => "ok",
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