
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $year = $_POST['year'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $price = $_POST['price'] ?? '';
    
    $file = 'books.csv';
    $data = [$title, $author, $year, $genre, $price];
    
    $handle = fopen($file, 'a');
    fputcsv($handle, $data);
    fclose($handle);
    
    echo "<script>alert('Книга успешно добавлена!'); window.location.href='index.php';</script>";
}
?>
