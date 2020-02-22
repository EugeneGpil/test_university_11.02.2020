<?php

namespace App\Controllers;

use App\Controllers\Base\Controller;
use App\Response;

class RoutesController extends Controller
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

        foreach (self::ROUTES as $route) {
            if ($route["url"] == $url && $route["method"] == $method) {

                $className = $route["class"];
                $needed = new $className();
                return $needed->{$route["function"]}($requestData);
            }
        }

        return Response::response(false, "Method not allowed");
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
