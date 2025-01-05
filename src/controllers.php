<?php
require_once("business.php");
require_once("image-utils.php");

function upload(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        try {
            $result = handle_image_upload();
            return $result;
        } catch (Exception $e) {
            $model['error'] = "Błąd przesyłania obrazu: " . $e->getMessage(); 
            return '/upload'; 
        }
    } else {
        return '/upload';
    }
}

function galeria(&$model) {
    try {
        $cats = get_pictures();
        $model['cats'] = $cats;
        return '/galeria';
    } catch (Exception $e) {
        $model['error'] = "Błąd pobierania galerii: " . $e->getMessage();
        return '/galeria';
    }
}

function index(&$model){
    return '/index';
}

function viewimage(&$model)
{
    $model['id'] = $_GET['id'];
    return '/viewimage';
}

function register(&$model)
{
    $user = [
        'username' => null,
        'email' => null,
        'password' => null,
        'error' => null
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($username) || empty($email) || empty($password)) {
                $user['error'] = 'Wszystkie pola są wymagane.';
                $model['user'] = $user;
                return 'register.phtml';
            }

            if (userExists($username, $email)) {
                $user['error'] = 'Użytkownik o podanym loginie lub e-mailu już istnieje.';
                $model['user'] = $user;
                return 'register.phtml';
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if (saveUser($username, $email, $hashedPassword)) {
                header("Location: /index");
                exit();
            } else {
                $user['error'] = 'Wystąpił błąd podczas rejestracji.';
            }
        } catch (Exception $e) {
            $user['error'] = 'Błąd rejestracji: ' . $e->getMessage();
        }
    }

    $model['user'] = $user;
    return 'register.phtml';
}

function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $identifier = $_POST['identifier'] ?? '';
            $password = $_POST['password'] ?? '';

            if (verifyUser($identifier, $password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_regenerate_id(true);

                $_SESSION['username'] = $identifier;
                $_SESSION['user'] = $identifier;
                $_SESSION['success_message'] = 'Zalogowano pomyślnie!';

                header("Location: /index");
                exit();
            } else {
                $model['error'] = "Nieprawidłowe dane logowania.";
            }
        } catch (Exception $e) {
            $model['error'] = "Błąd logowania: " . $e->getMessage();
        }
    }
    return 'login';
}

function logout() {
    unset($_SESSION['user']);
    session_unset(); //USUWA ZMIENNE SESJI
    session_destroy(); //NA SERWERZE
    setcookie(session_name(), '', time() - 3600, '/'); //NA URZĄDZENIU
    header("Location: /index");
    exit();
}
