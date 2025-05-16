<?php

namespace App\Controller;

use App\Model\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthController {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
        session_start();
    }

    public function registerForm() {
        echo $this->twig->render('Auth/Register.twig', [
            'message' => $_SESSION['message'] ?? null,
            'errors' => $_SESSION['errors'] ?? []
        ]);
        unset($_SESSION['message'], $_SESSION['errors']);
    }

    public function register() {
        $errors = $this->validateRegistration($_POST);
        
        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: ?route=register');
            exit;
        }

        try {
            User::create(
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role'] ?? 'customer'
            );
            $_SESSION['message'] = 'Регистрация успешна! Войдите в систему.';
            header('Location: ?route=login');
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Ошибка регистрации: ' . $e->getMessage()];
            header('Location: ?route=register');
        }
    }

    public function loginForm() {
        echo $this->twig->render('Auth/Login.twig', [
            'message' => $_SESSION['message'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['message'], $_SESSION['error']);
    }

    public function login() {
        $user = User::verify($_POST['username'], $_POST['password']);
        
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: /');
        } else {
            $_SESSION['error'] = 'Неверные учетные данные';
            header('Location: ?route=login');
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /');
    }

    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'Имя пользователя обязательно';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Пароли не совпадают';
        }
        
        return $errors;
    }
}