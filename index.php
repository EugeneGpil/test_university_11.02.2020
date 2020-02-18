<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";

echo json_encode(\App\Controllers\RoutesController::route());
