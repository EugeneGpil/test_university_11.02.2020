<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class Session extends Model
{
    private $relationsToSpeakersTableName = "session_speaker";
    private $relationsToParticipantsTableName = "session_participant";

    public function __construct()
    {
        $this->tableName = "session";

        $this->columnNames = [
            "id",
            "name",
            "time_of_event",
            "description",
            "number_of_seats"
        ];

        $this->relationTableColumnNames = [
            $this->relationsToSpeakersTableName => [
                "id",
                "session",
                "speaker"
            ],
            $this->relationsToParticipantsTableName => [
                "id",
                "session",
                "participant"
            ]
        ];
    }

    public function getByIdWithSpeaker($id)
    {
        return $this->getByIdWithRelations($id, $this->relationsToSpeakersTableName);
    }

    public function getByIdWithParticipants($id)
    {
        return $this->getByIdWithRelations($id, $this->relationsToParticipantsTableName);
    }

    public function getAllWithSpeakers()
    {
        return $this->getAllWithRelations($this->relationsToSpeakersTableName);
    }

    public function isSessionRelatedToParticipant($sessionId, $participantId)
    {
        return $this->isItRelated($this->relationsToParticipantsTableName, $sessionId, $participantId);
    }

    public function addParticipant($sessionId, $participantId)
    {
        return $this->addRelation($this->relationsToParticipantsTableName, $sessionId, $participantId);
    }
}
