<?php

require_once "config.php";

$DB = new PDO(
    "mysql:host=" . $CONFIG["database_server"] .
        ";dbname=" . $CONFIG["database_name"],
    $CONFIG["database_user"],
    $CONFIG["database_password"],
    [PDO::ATTR_TIMEOUT => $CONFIG["database_connection_timeout"]]
);
$DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);