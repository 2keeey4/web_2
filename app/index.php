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
        <form action="form.php" method="post" onsubmit="return validateForm()">
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
    <script>
        function validateForm() {
        const title = document.getElementById('title').value;
        const author = document.getElementById('author').value;
        const genre = document.getElementById('genre').value;

        if (title.length > 100 || author.length > 100 || genre.length > 100) {
            alert('Поля "Название книги", "Автор" и "Жанр" не могут быть длиннее 100 символов.');
            return false;
        }

        const tagPattern = /<[^>]*>/;
        if (tagPattern.test(title) || tagPattern.test(author) || tagPattern.test(genre)) {
            alert('Поля не могут содержать HTML-теги.');
            return false;
        }

        const digitPattern = /\d{3,}/;
        if (digitPattern.test(title) || digitPattern.test(author) || digitPattern.test(genre)) {
            alert('Поля не могут содержать много цифр.');
            return false;
        }

        if (title.length < 2 || author.length < 2 || genre.length < 2) {
            alert('Поля должны содержать минимум 2 символа.');
            return false;
        }

        const specialCharPattern = /[&<>"'`]/;
        if (specialCharPattern.test(title) || specialCharPattern.test(author) || specialCharPattern.test(genre)) {
            alert('Поля не могут содержать специальные символы, такие как &, <, >, ", \', `.');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>