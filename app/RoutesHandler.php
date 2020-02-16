<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/connection.php";

class RoutesHandler
{
    private $routes = [
        [
            "url" => "/api/table",
            "method" => "GET",
            "path_to_class" => "/app/",
            "class_namespace" => "App\\",
            "class" => "TablesHandler",
            "function" => "getTable"
        ],
        [
            "url" => "/api/sessionsubscribe",
            "method" => "POST",
            "path_to_class" => "/app/Controllers/",
            "class_namespace" => "App\\Controllers\\",
            "class" => "SessionController",
            "function" => "subscribe"
        ]
    ];

    public function route()
    {
        $url = $this->getUrl();
        $method = $_SERVER["REQUEST_METHOD"];

        if ($method == "GET") {
            $requestData = $_REQUEST;
        } else {
            $requestData = json_decode(file_get_contents("php://input"), true);
        }

        foreach ($this->routes as $route) {
            if ($route["url"] == $url && $route["method"] == $method) {
                require_once $_SERVER["DOCUMENT_ROOT"] . $route["path_to_class"] . $route["class"] . ".php";

                $className = $route["class_namespace"] . $route["class"];
                $needed = new $className();
                return $needed->{$route["function"]}($requestData);
            }
        }

        return [
            "status" => "error",
            "message" => "Неправильный адрес или метод"
        ];
    }

    private function getUrl()
    {
        $url = $_SERVER["REQUEST_URI"];
        $questionMarkPosition = strpos($url, '?');
        if ($questionMarkPosition === false) {
            return $url;
        }
        return substr($url, 0, $questionMarkPosition);
    }
}
