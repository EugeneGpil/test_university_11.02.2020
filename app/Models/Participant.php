<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class Participant extends Model
{
    public function __construct()
    {
        $this->tableName = "participant";

        $this->columnNames = [
            "id",
            "email",
            "name"
        ];
    }
}