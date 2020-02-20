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
            return self::response(true, $news->getAll());
        }

        $id = $this->getInt($request["id"]);

        if (!$id) {
            return self::response(false, "Некорректное значение id");
        }

        return self::response(true, $news->getById($id));
    }
}