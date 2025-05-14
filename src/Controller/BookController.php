<?php

namespace App\Controller;

use App\Model\Book;
use App\Core\db;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BookController {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }

    public function index() {
        $books = Book::getAll();
        echo $this->twig->render('Book.twig', ['books' => $books]);
    }

    public function submit() {
        $data = $_POST;

        $errors = Book::validate($data);

        if ($errors) {
            echo "<script>alert('" . implode("\\n", $errors) . "'); window.location.href='/'</script>";
            return;
        }

        Book::save($data);
        echo "<script>alert('Книга добавлена'); window.location.href='/'</script>";
    }
}
