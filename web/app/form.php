<?php
$pdo = new PDO("mysql:host=db;dbname=mydb;charset=utf8", "user", "pass");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $genre = trim($_POST['genre'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);

    $errors = [];

    $requiredFields = [
        'Название книги' => $title,
        'Автор' => $author,
        'Год издания' => $year,
        'Жанр' => $genre,
        'Цена' => $price,
        'Количество' => $quantity,
    ];

    foreach ($requiredFields as $fieldName => $fieldValue) {
        if (empty($fieldValue) && $fieldValue !== 0) {
            $errors[] = "Поле '$fieldName' обязательно для заполнения.";
        }
    }

    if (!preg_match('/^\d{4}$/', $year) || $year < 1500 || $year > 2025) {
        $errors[] = "Год издания должен быть числом от 1500 до 2025.";
    }

    if ($price < 0) {
        $errors[] = "Цена не может быть отрицательной.";
    }

    if ($quantity < 1) {
        $errors[] = "Количество должно быть минимум 1.";
    }

    function validateField($field, $fieldName) {
        if (strlen($field) > 100) return "$fieldName не может быть длиннее 100 символов.";
        if (preg_match('/<[^>]*>/', $field)) return "$fieldName не может содержать HTML-теги.";
        if (preg_match('/\d{3,}/', $field)) return "$fieldName не может содержать много цифр.";
        if (strlen($field) < 2) return "$fieldName должен содержать минимум 2 символа.";
        if (preg_match('/[&<>"\'\`]/', $field)) return "$fieldName не может содержать специальные символы.";
        return null;
    }

    if (empty($errors)) {
        $errors[] = validateField($title, 'Название книги');
        $errors[] = validateField($author, 'Автор');
        $errors[] = validateField($genre, 'Жанр');
    }

    $errors = array_filter($errors);

    if (count($errors) > 0) {
        echo "<script>alert('" . implode("\\n", $errors) . "'); window.location.href='index.php';</script>";
    } else {
        $stmt = $pdo->prepare("SELECT id, quantity FROM books WHERE title = ? AND author = ? AND year = ? AND genre = ? AND price = ?");
        $stmt->execute([$title, $author, $year, $genre, $price]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE books SET quantity = ? WHERE id = ?");
            $update->execute([$newQty, $existing['id']]);
        } else {
            $insert = $pdo->prepare("INSERT INTO books (title, author, year, genre, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$title, $author, $year, $genre, $price, $quantity]);
        }

        echo "<script>alert('Книга успешно добавлена!'); window.location.href='index.php';</script>";
    }
}
?>
