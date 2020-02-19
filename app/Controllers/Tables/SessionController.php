<?php

namespace App\Controllers\Tables;

use App\Models\Session;
use App\Models\Participant;
use App\Controllers\Base\Controller;

class SessionController extends Controller
{
    public function getTable($request)
    {
        $session = new Session();

        if (!isset($request["id"])) {
            return [
                "stutus" => "ok",
                "payload" => $session->getAllWithSpeakers()
            ];
        }

        $id = $this->getInt($request["id"]);

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

        $sessionId = $this->getInt($requestData["sessionId"]);

        if (!$sessionId) {
            return [
                "status" => "error",
                "message" => "Некорректное значение ID лекции"
            ];
        }

        $participant = new Participant();
        $participantByEmail = $participant->getByColumn("email", $requestData["userEmail"]);

        if (empty($participantByEmail)) {
            return [
                "status" => "error",
                "message" => "Пользователь с данной почтой не найдет"
            ];
        }

        $session = new Session();
        $sessionByIdWithPartisipants = $session->getByIdWithParticipants($requestData["sessionId"]);

        if (empty($sessionByIdWithPartisipants)) {
            return [
                "status" => "error",
                "message" => "Лекция с данным ID не найдена"
            ];
        }

        if ($this->isValueExist($sessionByIdWithPartisipants["Participants"], "ID", $participantByEmail["ID"])) {
            return [
                "status" => "ok",
                "message" => "Вы уже записывались"
            ];
        }

        if (count($sessionByIdWithPartisipants["Participants"]) >= $sessionByIdWithPartisipants["NumberOfSeats"]) {
            return [
                "status" => "error",
                "message" => "Извините, все места заняты"
            ];
        }

        $session->addParticipant($requestData["sessionId"], $participantByEmail["ID"]);

        return [
            "status" => "ok",
            "message" => "Спасибо, вы успешно записаны!"
        ];
    }

    private function isValueExist($array, $key, $value) : bool
    {
        foreach ($array as $element) {
            if ($element[$key] == $value) {
                return true;
            }
        }
        return false;
    }
}
