<?php

namespace App\Controller;

use App\Model\Order;
use App\Model\Book;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class OrderController {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        session_start();
    }

    public function orderForm() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?route=login');
            exit;
        }

        $books = Book::getAll();
        echo $this->twig->render('Order.twig', [
            'books' => $books,
            'user' => $_SESSION['user']
        ]);
    }

    public function submit() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?route=login');
            exit;
        }

        $errors = Order::validate($_POST);
        if ($errors) {
            echo "<script>alert('" . implode("\\n", $errors) . "'); window.location.href='?route=order'</script>";
            return;
        }

        $result = Order::place($_POST);
        echo "<script>alert('$result'); window.location.href='?route=order'</script>";
    }
}