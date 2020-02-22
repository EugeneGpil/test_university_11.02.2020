<?php

namespace App;

class Response
{
    public static function response($status, $payload, $message = null)
    {
        $response["status"] = $status ? "ok" : "error";
        header('Content-Type: application/json');
        if ($payload && is_string($payload)) {
            $response["message"] = $payload;
            return $response;
        }

        if ($payload && !is_array($payload)) {
            return [
                "status" => "error",
                "message" => "Внутренняя ошибка. Payload не является массивом"
            ];
        }
        if ($payload || $payload == []) {
            $response["payload"] = $payload;
        }
        if ($message) {
            $response["message"] = $message;
        }
        return $response;
    }
}