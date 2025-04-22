<?php
$pdo = new PDO('mysql:host=db;dbname=mydb', 'user', 'pass');

$books = $pdo->query("SELECT * FROM books")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Книжный магазин</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="container">
        <h2>Добавить книгу</h2>
        <form action="form.php" method="post" onsubmit="return validateForm()">
            <label for="title">Название книги:</label>
            <input type="text" id="title" name="title" required>

            <label for="author">Автор:</label>
            <input type="text" id="author" name="author" required>

            <label for="year">Год издания:</label>
            <input type="number" id="year" name="year" min="1500" max="2025" required>

            <label for="genre">Жанр:</label>
            <input type="text" id="genre" name="genre" required>

            <label for="price">Цена (руб.):</label>
            <input type="number" step="0.01" id="price" name="price" required>

            <label for="quantity">Количество:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <input type="submit" value="Добавить">
        </form>

        <h2>Список книг</h2>
        <table>
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Год</th>
                <th>Жанр</th>
                <th>Цена (руб.)</th>
                <th>Количество</th>
            </tr>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= $book['year'] ?></td>
                    <td><?= htmlspecialchars($book['genre']) ?></td>
                    <td><?= $book['price'] ?></td>
                    <td><?= $book['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
    function validateForm() {
        const title = document.getElementById('title').value;
        const author = document.getElementById('author').value;
        const genre = document.getElementById('genre').value;

        if ([title, author, genre].some(x => x.length < 2 || x.length > 100)) {
            alert('Поля "Название", "Автор" и "Жанр" должны содержать от 2 до 100 символов.');
            return false;
        }

        const tagPattern = /<[^>]*>/;
        if ([title, author, genre].some(x => tagPattern.test(x))) {
            alert('Поля не могут содержать HTML-теги.');
            return false;
        }

        const digitPattern = /\d{3,}/;
        if ([title, author, genre].some(x => digitPattern.test(x))) {
            alert('Поля не могут содержать много цифр.');
            return false;
        }

        const specialCharPattern = /[&<>"'`]/;
        if ([title, author, genre].some(x => specialCharPattern.test(x))) {
            alert('Поля не могут содержать спецсимволы.');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>
