<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class Session extends Model
{
    private $RelationsToSpeakersTableName = "session_speaker";

    public function __construct()
    {
        $this->tableName = "session";
    }

    public function getByIdWithSpeaker($id)
    {
        return $this->getByIdWithRelations($id, $this->RelationsToSpeakersTableName);
    }

    public function getAllWithSpeakers()
    {
        return $this->getAllWithRelations($this->RelationsToSpeakersTableName);
    }
}
