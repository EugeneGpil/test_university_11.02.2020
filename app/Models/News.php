<?php

namespace App\Models;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Base/Model.php";

use App\Models\Base\Model;

class News extends Model
{
    private $id;
    private $name;
    private $timeOfEvent;
    private $description;
    private $numberOfSeats;

    public function __construct($id = null)
    {
        $this->tableName = "news";

        if (!$id) { return; }

        $newsById = $this->getById($id);
        $this->id = $newsById["id"];

    }
}
