<?php

namespace App\Controllers\Tables;

use App\Models\News;
use App\Controllers\Base\Controller;
use App\Response;

class NewsController extends Controller
{
    public function getTable($request)
    {
        $news = new News();

        if (!isset($request["id"])) {
            return Response::response(true, $news->getAll());
        }

        $id = $this->getValidId($request["id"]);

        if (!$id) {
            return Response::response(false, "Некорректное значение id");
        }

        return Response::response(true, $news->getById($id));
    }
}
