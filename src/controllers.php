<?php
require_once("business.php");
require_once("image-utils.php");

function upload(&$model){
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])){
        $result = handle_image_upload();
        return $result;
    }
    else{
        return '/upload';
    }
}

function galeria(&$model){
    $cats = get_pictures();
    $model['cats'] = $cats;
    return '/galeria';
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
        //DANE
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        //WALIDACJA
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

        //ZAPISANIE W BAZIE
        if (saveUser($username, $email, $hashedPassword)) {
            header("Location: /index");
            exit();
        } else {
            $user['error'] = 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie.';
        }
    }

    $model['user'] = $user;
    return 'register.phtml';
}

function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // DANE
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        //POPRAWNOŚĆ DANYCH
        if (verifyUser($identifier, $password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            //DANE -> SESJA
            $_SESSION['username'] = $identifier;
            $_SESSION['user'] = $identifier;
            $_SESSION['success_message'] = 'Zalogowano pomyślnie!';

            header("Location: /index");
            exit();
        } else {
            $model['error'] = "Nieprawidłowe dane logowania.";
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
