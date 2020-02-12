<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/RoutesHandler.php";

$routesHandler = new RoutesHandler();
header('Content-Type: application/json');
echo json_encode($routesHandler->route());
