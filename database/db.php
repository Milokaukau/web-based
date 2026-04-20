<?php
require_once dirname(__DIR__) . '/config.php';
function db(){
    static $db;

    if(!$db){
        $db = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4".";port=".DB_PORT,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // PHP throws an exception if something went wrong
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Make the data fetched in the form of object instead of array
                PDO::ATTR_EMULATE_PREPARES => false // Instead of PDO, let SQL bind the value into "?"
            ]
        );
    }

    return $db;
}