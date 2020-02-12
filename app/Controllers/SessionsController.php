<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/Helpers/NumericHelper.php";
use App\Helpers\NumericHelper;

class SessionsController
{
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
