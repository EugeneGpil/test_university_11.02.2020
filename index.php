<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/RoutesHandler.php';

$routesHandler = new RoutesHandler();
echo json_encode($routesHandler->route());