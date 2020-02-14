<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class Session extends Model
{
    public function __construct()
    {
        $this->tableName = "session";
    }

    public function getByIdWithSpeaker($id)
    {
        return $this->getByIdWithRelations($id, "session_speaker");
    }
}
