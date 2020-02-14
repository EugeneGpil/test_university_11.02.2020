<?php

namespace App\Controllers;

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Models/Session.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Helpers/NumericHelper.php";

use App\Models\Session;
use App\Helpers\NumericHelper;

class SessionController
{
    public function getTable($request)
    {
        $session = new Session();

        if (!isset($request["id"])) {
            return [
                "stutus" => "ok",
                "payload" => $session->getAll()
            ];
        }

        $id = NumericHelper\getInt($request["id"]);

        if (!$id) {
            return [
                "status" => "error",
                "message" => "Некорректное значение id"
            ];
        }

        return [
            "status" => "ok",
            "payload" => $session->getByIdWithSpeaker($id)
        ];
    }

    public function subscribe($requestData)
    {
        if (!isset($requestData["userEmail"]) || !$requestData["userEmail"]) {
            return [
                "status" => "error",
                "message" => "Почта не указана"
            ];
        }

        if (!isset($requestData["sessionId"]) || !$requestData["sessionId"]) {
            return [
                "status" => "error",
                "message" => "ID лекции не указан"
            ];
        }

        $sessionId = NumericHelper\getInt($requestData["sessionId"]);

        if (!$sessionId) {
            return [
                "status" => "error",
                "message" => "Некорректное значение ID лекции"
            ];
        }

        global $DB;
        $session = $DB->query("SELECT `NumberOfRecorded`, `NumberOfSeats` FROM `Session` WHERE `ID` = '" . $sessionId . "'");
        $session = $session->fetchAll(PDO::FETCH_ASSOC);

        return $session;
    }
}
