<?php

namespace App\Core;

use App\Controller\BookController;
use App\Controller\OrderController;
use App\Controller\AuthController;
use App\Controller\ReportController;

class Router {
    public function run() {
        $route = $_GET['route'] ?? '';

        switch ($route) {
            case 'book/add':
                (new BookController())->submit();
                break;
            case 'order':
                (new OrderController())->orderForm();
                break;
            case 'order/submit':
                (new OrderController())->submit();
                break;
            case 'register':
                (new AuthController())->registerForm();
                break;
            case 'register/submit':
                (new AuthController())->register();
                break;
            case 'login':
                (new AuthController())->loginForm();
                break;
            case 'login/submit':
                (new AuthController())->login();
                break;
            case 'logout':
                (new AuthController())->logout();
                break;
            case 'report/csv':
                (new ReportController())->generateCSV();
                break;
            case 'report/pdf':
                (new ReportController())->generatePDF();
                break;
            case 'report/excel':
                (new ReportController())->generateXLS();
                break;
            
            default:
                (new BookController())->index();
        }
    }
}