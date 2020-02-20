<?php

namespace App\Controllers\Base;

abstract class Controller
{
    protected static function getInt($val) {
        if (is_numeric($val)) {
            return (int) round($val + 0);
        }
        return 0;
    }

    protected static function response($status, $payload, $message = null) {
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