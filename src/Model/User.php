<?php

namespace App\Model;

use App\Core\db;

class User {
    public static function create($username, $email, $password, $role = 'customer') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $pdo = db::connect();
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword, $role]);
    }

    public static function findByUsername($username) {
        $pdo = db::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function verify($username, $password) {
        $user = self::findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}