<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class News extends Model
{
    public function __construct()
    {
        $this->tableName = "news";

        $this->columnNames = [
            "id",
            "participant_id",
            "news_title",
            "news_message",
            "likes_counter"
        ];
    }
}
