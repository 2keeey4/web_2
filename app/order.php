<?php
$pdo = new PDO("mysql:host=db;dbname=mydb;charset=utf8", "user", "pass");
$books = $pdo->query("SELECT id, title, quantity FROM books")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ книги</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="container">
        <h2>Оформление заказа</h2>
        <form method="post">
            <label>ФИО:</label>
            <input type="text" name="full_name" required>

            <label>Телефон:</label>
            <input type="text" name="phone" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Книга:</label>
            <select name="book_id" required>
                <?php foreach ($books as $book): ?>
                    <option value="<?= $book['id'] ?>" <?= $book['quantity'] == 0 ? 'disabled style="color:red;"' : '' ?>>
                        <?= htmlspecialchars($book['title']) ?> <?= $book['quantity'] == 0 ? '(нет в наличии)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Купить">
        </form>
    </div>    
</body>
</html>
