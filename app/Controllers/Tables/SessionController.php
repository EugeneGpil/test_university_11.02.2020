<?php

namespace App\Controllers\Tables;

use App\Models\Session;
use App\Models\Participant;
use App\Controllers\Base\Controller;
use App\Response;

class SessionController extends Controller
{
    public function getTable($request)
    {
        $session = new Session();

        if (!isset($request["id"])) {
            return Response::response(true, $session->getAllWithSpeakers());
        }

        $id = $this->getValidId($request["id"]);

        if (!$id) {
            return Response::response(false, "Некорректное значение id");
        }

        return Response::response(true, $session->getByIdWithSpeaker($id));
    }

    public function subscribe($requestData)
    {
        if (!isset($requestData["userEmail"]) || !$requestData["userEmail"]) {
            return Response::response(false, "Почта не указана");
        }

        if (!isset($requestData["sessionId"]) || !$requestData["sessionId"]) {
            return Response::response(false, "ID лекции не указан");
        }

        $sessionId = $this->getValidId($requestData["sessionId"]);

        if (!$sessionId) {
            return Response::response(false, "Некорректное значение ID лекции");
        }

        $participant = new Participant();
        $participantByEmail = $participant->getByColumn("email", $requestData["userEmail"]);

        if (empty($participantByEmail)) {
            return Response::response(false, "Пользователь с данной почтой не найдет");
        }

        $session = new Session();
        $sessionByIdWithPartisipants = $session->getByIdWithParticipants($requestData["sessionId"]);

        if (empty($sessionByIdWithPartisipants)) {
            return Response::response(false, "Лекция с данным ID не найдена");
        }

        if ($this->isValueExist($sessionByIdWithPartisipants["Participants"], "ID", $participantByEmail["ID"])) {
            return Response::response(true, "Вы уже записывались");
        }

        if (count($sessionByIdWithPartisipants["Participants"]) >= $sessionByIdWithPartisipants["NumberOfSeats"]) {
            return Response::response(false, "Извините, все места заняты");
        }

        $session->addParticipant($requestData["sessionId"], $participantByEmail["ID"]);
        return Response::response(true, "Спасибо, вы успешно записаны!");
    }

    private function isValueExist($array, $key, $value): bool
    {
        foreach ($array as $element) {
            if ($element[$key] == $value) {
                return true;
            }
        }
        return false;
    }
}
