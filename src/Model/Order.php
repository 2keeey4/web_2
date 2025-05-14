<?php

namespace App\Model;

use App\Core\db;

class Order {
    public static function validate($data) {
        $errors = [];

        if (!preg_match('/^[А-Яа-яЁёA-Za-z\s]{5,}$/u', $data['full_name'] ?? '')) {
            $errors[] = 'ФИО должно содержать минимум 5 букв.';
        }

        if (!preg_match('/^\+?\d{10,}$/', $data['phone'] ?? '')) {
            $errors[] = 'Телефон должен содержать минимум 10 цифр.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email.';
        }

        if (empty($data['book_id'])) {
            $errors[] = 'Книга не выбрана.';
        }

        return $errors;
    }

    public static function place($data) {
        $pdo = db::connect();

        $stmt = $pdo->prepare("SELECT quantity FROM books WHERE id = ?");
        $stmt->execute([$data['book_id']]);
        $book = $stmt->fetch();

        if (!$book || $book['quantity'] <= 0) {
            return 'Книга отсутствует.';
        }

        $insert = $pdo->prepare("INSERT INTO orders (full_name, phone, email, book_id) VALUES (?, ?, ?, ?)");
        $insert->execute([$data['full_name'], $data['phone'], $data['email'], $data['book_id']]);

        $update = $pdo->prepare("UPDATE books SET quantity = quantity - 1 WHERE id = ?");
        $update->execute([$data['book_id']]);

        return 'Заказ оформлен!';
    }
}
