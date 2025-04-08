<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $year = $_POST['year'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $price = $_POST['price'] ?? '';

    $requiredFields = [
        'Название книги' => $title,
        'Автор' => $author,
        'Год издания' => $year,
        'Жанр' => $genre,
        'Цена' => $price,
    ];

    $errors = [];
    foreach ($requiredFields as $fieldName => $fieldValue) {
        if (empty($fieldValue)) {
            $errors[] = "Поле '$fieldName' обязательно для заполнения.";
        }
    }

    if (!empty($year)) {
        if (!preg_match('/^\d{4}$/', $year)) {
            $errors[] = "Год издания должен быть четырёхзначным числом.";
        }
        elseif ($year < 1500 || $year > 2025) {
            $errors[] = "Год издания должен быть в диапазоне от 1500 до 2025.";
        }
    }

    if (!empty($price) && $price < 0) {
        $errors[] = "Цена не может быть отрицательной.";
    }

    function validateField($field, $fieldName) {
        if (strlen($field) > 100) {
            return "$fieldName не может быть длиннее 100 символов.";
        }
        if (preg_match('/<[^>]*>/', $field)) {
            return "$fieldName не может содержать HTML-теги.";
        }
        if (preg_match('/\d{3,}/', $field)) {
            return "$fieldName не может содержать много цифр.";
        }
        if (strlen($field) < 2) {
            return "$fieldName должен содержать минимум 2 символа.";
        }
        if (preg_match('/[&<>"\'\`]/', $field)) {
            return "$fieldName не может содержать специальные символы, такие как &, <, >, \", ', `.";
        }
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
        $file = 'books.csv';
        $data = [$title, $author, $year, $genre, $price];
        
        $handle = fopen($file, 'a');
        fputcsv($handle, $data);
        fclose($handle);
        
        echo "<script>alert('Книга успешно добавлена!'); window.location.href='index.php';</script>";
    }
}
?>