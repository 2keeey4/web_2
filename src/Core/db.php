<?php

namespace App\Core;

use PDO;

class db {
    public static function connect() {
        return new PDO("mysql:host=db;dbname=mydb;charset=utf8", "user", "pass", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
