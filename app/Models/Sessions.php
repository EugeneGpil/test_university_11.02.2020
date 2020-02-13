<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class Session extends Model
{
    public function __construct()
    {
        $this->tableName = "Session";
    }

    public function getAllWithSpeakers()
    {
    }
}
