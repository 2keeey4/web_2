<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книжный магазин</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Добавить книгу</h2>
        <form action="form.php" method="post">
            <label for="title">Название книги:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="author">Автор:</label>
            <input type="text" id="author" name="author" required>
            
            <label for="year">Год издания:</label>
            <input type="number" id="year" name="year" min="1500" max="2025" step="1" required>
            
            <label for="genre">Жанр:</label>
            <input type="text" id="genre" name="genre" required>
            
            <label for="price">Цена (руб.):</label>
            <input type="number" step="0.1" id="price" name="price" required>
            
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
            </tr>
            <?php
            if (($handle = fopen("books.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    echo "<tr><td>" . implode("</td><td>", $data) . "</td></tr>";
                }
                fclose($handle);
            }
            ?>
        </table>
    </div>
</body>
</html>
