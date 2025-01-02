<?php
require_once("business.php");
require_once("image-utils.php");

function cats(&$model){
    return '/cats';
}

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

function index() {
    require '../views/index.phtml';
}



function viewimage(&$model)
{
    $model['id'] = $_GET['id'];
    return '/viewimage';
}

function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Walidacja danych
        if (strlen($username) < 3) {
            echo "Nazwa użytkownika musi mieć co najmniej 3 znaki.";
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Nieprawidłowy format adresu e-mail.";
            return;
        }
        if (strlen($password) < 6) {
            echo "Hasło musi mieć co najmniej 6 znaków.";
            return;
        }

        // Sprawdzenie, czy użytkownik istnieje
        $existingUser = userExists($username, $email);
        if ($existingUser) {
            if ($existingUser['username'] === $username) {
                echo "Nazwa użytkownika jest zajęta.";
            } elseif ($existingUser['email'] === $email) {
                echo "E-mail jest już zarejestrowany.";
            }
            return;
        }

        // Hashowanie hasła i zapis użytkownika
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        if (saveUser($username, $email, $hashedPassword)) {
            echo "Rejestracja zakończona pomyślnie!";
        } else {
            echo "Wystąpił błąd podczas rejestracji.";
        }
    } else {
        require '../views/register.phtml';
    }
}

function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        if (verifyUser($identifier, $password)) {
            session_start();
            $_SESSION['user'] = $identifier;
            echo "Zalogowano pomyślnie!";
        } else {
            echo "Nieprawidłowe dane logowania.";
        }
    } else {
        require '../views/login.phtml';
    }
}
