<?php

namespace App\Core;

use App\Controller\BookController;
use App\Controller\OrderController;

class Router {
    public function run() {
        $uri = $_GET['route'] ?? '';

        switch ($uri) {
            case 'order':
                (new OrderController())->orderForm();
                break;
            case 'order/submit':
                (new OrderController())->submit();
                break;
            case 'book/add':
                (new BookController())->submit();
                break;
            default:
                (new BookController())->index();
        }
    }
}
