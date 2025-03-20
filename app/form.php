<?php
$csv_file = "books.csv";

//post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"] ?? '';
    $author = $_POST["author"] ?? '';
    $year = $_POST["year"] ?? '';
    $genre = $_POST["genre"] ?? '';
    $price = $_POST["price"] ?? '';
    $format = $_POST["format"] ?? '';

    if ($title && $author && $year && $genre && $price && $format) {
        $file = fopen($csv_file, "a");
        fputcsv($file, [$title, $author, $year, $genre, $price, $format]);
        fclose($file);
        echo "Книга успешно добавлена!";
    } else {
        echo "Ошибка: Все поля должны быть заполнены!";
    }
    exit;
}

// get
if (file_exists($csv_file)) {
    $file = fopen($csv_file, "r");
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        echo "<tr>";
        foreach ($data as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    fclose($file);
}
?>
