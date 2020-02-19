<?php

namespace App\Controllers\Tables;

use App\Models\News;
use App\Helpers\NumericHelper;
use App\Controllers\Base\Controller;

class NewsController extends Controller
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

        $id = $this->getInt($request["id"]);

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