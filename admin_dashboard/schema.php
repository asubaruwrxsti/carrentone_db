#!/usr/bin/env php
<?php
    require_once "database.php";
    require_once "vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $db = new DB($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
    $res = $db->execute_query("SHOW TABLES");
    var_dump($res);