<?php

namespace App\Controllers;

class RoutesController
{
    private const ROUTES = [
        [
            "url" => "/api/table",
            "method" => "GET",
            "class" => "\\App\\Controllers\\TablesController",
            "function" => "getTable"
        ],
        [
            "url" => "/api/sessionsubscribe",
            "method" => "POST",
            "class" => "\\App\\Controllers\\Tables\\SessionController",
            "function" => "subscribe"
        ]
    ];

    public static function route()
    {
        $url = self::getUrl();
        $method = $_SERVER["REQUEST_METHOD"];

        if ($method == "GET") {
            $requestData = $_REQUEST;
        } else {
            $requestData = json_decode(file_get_contents("php://input"), true);
        }

        header('Content-Type: application/json');

        foreach (self::ROUTES as $route) {
            if ($route["url"] == $url && $route["method"] == $method) {

                $className = $route["class"];
                $needed = new $className();
                return $needed->{$route["function"]}($requestData);
            }
        }

        return [
            "status" => "error",
            "message" => "Неправильный адрес или метод"
        ];
    }

    private static function getUrl()
    {
        $url = $_SERVER["REQUEST_URI"];
        $questionMarkPosition = strpos($url, '?');
        if ($questionMarkPosition === false) {
            return $url;
        }
        return substr($url, 0, $questionMarkPosition);
    }
}
