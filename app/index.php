<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Книжный магазин</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="container">
        <h2>Добавить книгу</h2>
        
        <form id="bookForm">
            <label>Название книги:</label>
            <input type="text" name="title" required>

            <label>Автор:</label>
            <input type="text" name="author" required>
            <label>Год издания:</label>
            <select name="year" required>
                <?php
                $currentYear = date("Y");
                for ($year = $currentYear; $year >= 1900; $year--) {
                    echo "<option value='$year'>$year</option>";
                }
                ?>
            </select>


            <label>Жанр:</label>
            <input type="text" name="genre" required>

            <label>Цена (₽):</label>
            <input type="number" name="price" min="1" step="0.01" required>

            <label>Формат книги:</label>
            <select name="format" required>
                <option value="Твердый переплет">Твердый переплет</option>
                <option value="Мягкий переплет">Мягкий переплет</option>
                <option value="Электронная">Электронная</option>
                <option value="Аудиокнига">Аудиокнига</option>
            </select>

            <button type="submit">Добавить</button>
        </form>

        <h2>Список книг</h2>
        <table id="booksTable">
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Год</th>
                <th>Жанр</th>
            </tr>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            function loadBooks() {
                $.get("form.php", function (data) {
                    $("#booksTable").html(data);
                });
            }

            loadBooks();

            $("#bookForm").submit(function (event) {
                event.preventDefault(); 

                $.post("form.php", $(this).serialize(), function (response) {
                    alert(response); 
                    $("#bookForm")[0].reset();  
                    loadBooks();  
                });
            });
        });
    </script>

</body>
</html>
