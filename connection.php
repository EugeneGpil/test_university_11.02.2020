<?php

require_once "config.php";

$DB = new PDO(
    "mysql:host=" . $CONFIG["database_server"] .
        ";dbname=" . $CONFIG["database_name"],
    $CONFIG["database_user"],
    $CONFIG["database_password"],
    [PDO::ATTR_TIMEOUT => $CONFIG["database_connection_timeout"]]
);
