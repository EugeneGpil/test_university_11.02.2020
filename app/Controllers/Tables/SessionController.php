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
            return self::response(true, $session->getAllWithSpeakers());
        }

        $id = $this->getInt($request["id"]);

        if (!$id) {
            return self::response(false, "Некорректное значение id");
        }

        return self::response(true, $session->getByIdWithSpeaker($id));
    }

    public function subscribe($requestData)
    {
        if (!isset($requestData["userEmail"]) || !$requestData["userEmail"]) {
            return self::response(false, "Почта не указана");
        }

        if (!isset($requestData["sessionId"]) || !$requestData["sessionId"]) {
            return self::response(false, "ID лекции не указан");
        }

        $sessionId = $this->getInt($requestData["sessionId"]);

        if (!$sessionId) {
            return self::response(false, "Некорректное значение ID лекции");
        }

        $participant = new Participant();
        $participantByEmail = $participant->getByColumn("email", $requestData["userEmail"]);

        if (empty($participantByEmail)) {
            return self::response(false, "Пользователь с данной почтой не найдет");
        }

        $session = new Session();
        $sessionByIdWithPartisipants = $session->getByIdWithParticipants($requestData["sessionId"]);

        if (empty($sessionByIdWithPartisipants)) {
            return self::response(false, "Лекция с данным ID не найдена");
        }

        if ($this->isValueExist($sessionByIdWithPartisipants["Participants"], "ID", $participantByEmail["ID"])) {
            return self::response(true, "Вы уже записывались");
        }

        if (count($sessionByIdWithPartisipants["Participants"]) >= $sessionByIdWithPartisipants["NumberOfSeats"]) {
            return self::response(false, "Извините, все места заняты");
        }

        $session->addParticipant($requestData["sessionId"], $participantByEmail["ID"]);
        return self::response(true, "Спасибо, вы успешно записаны!");
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
