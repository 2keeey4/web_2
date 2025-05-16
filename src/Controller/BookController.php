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
        session_start();
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?route=login');
            exit;
        }

        $search = $_GET['search'] ?? '';
        $books = Book::getAll($search);
        
        echo $this->twig->render('Book.twig', [
            'books' => $books,
            'search' => $search,
            'user' => $_SESSION['user'] ?? null,
            'is_admin' => ($_SESSION['user']['role'] ?? '') === 'admin'
        ]);
    }

    public function submit() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }

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