<?php

namespace App\Model;

use App\Core\db;

class Book {
    public static function getAll() {
        return db::connect()->query("SELECT * FROM books")->fetchAll();
    }

    public static function validate($data) {
        $errors = [];

        $required = ['title', 'author', 'genre'];
        foreach ($required as $field) {
            if (empty($data[$field]) || strlen($data[$field]) < 2 || strlen($data[$field]) > 100) {
                $errors[] = "Поле $field должно содержать от 2 до 100 символов.";
            }
            if (preg_match('/<[^>]*>/', $data[$field]) || preg_match('/[&<>"\'`]/', $data[$field])) {
                $errors[] = "Поле $field содержит недопустимые символы.";
            }
        }

        if ($data['year'] < 1500 || $data['year'] > 2025) $errors[] = "Некорректный год.";
        if ($data['price'] < 0) $errors[] = "Цена не может быть отрицательной.";
        if ($data['quantity'] < 1) $errors[] = "Количество должно быть минимум 1.";

        return $errors;
    }

    public static function save($data) {
        $pdo = db::connect();

        $stmt = $pdo->prepare("SELECT id, quantity FROM books WHERE title = ? AND author = ? AND year = ? AND genre = ? AND price = ?");
        $stmt->execute([$data['title'], $data['author'], $data['year'], $data['genre'], $data['price']]);
        $book = $stmt->fetch();

        if ($book) {
            $newQty = $book['quantity'] + $data['quantity'];
            $update = $pdo->prepare("UPDATE books SET quantity = ? WHERE id = ?");
            $update->execute([$newQty, $book['id']]);
        } else {
            $insert = $pdo->prepare("INSERT INTO books (title, author, year, genre, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$data['title'], $data['author'], $data['year'], $data['genre'], $data['price'], $data['quantity']]);
        }
    }
}
