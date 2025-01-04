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
        'error' => null  // Usunięto `repeat_password`
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pobieranie danych z formularza
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Walidacja wymaganych pól
        if (empty($username) || empty($email) || empty($password)) {
            $user['error'] = 'Wszystkie pola są wymagane.';
            $model['user'] = $user;
            return 'register.phtml';
        }

        // Sprawdzenie, czy użytkownik już istnieje
        if (userExists($username, $email)) {
            $user['error'] = 'Użytkownik o podanym loginie lub e-mailu już istnieje.';
            $model['user'] = $user;
            return 'register.phtml';
        }

        // Hashowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Zapisanie użytkownika w bazie
        if (saveUser($username, $email, $hashedPassword)) {
            header("Location: /index");  // Przekierowanie po sukcesie
            exit();
        } else {
            $user['error'] = 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie.';
        }
    }

    $model['user'] = $user;
    return 'register.phtml';  // Widok formularza rejestracji
}


function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        // Sprawdzenie poprawności danych logowania
        if (verifyUser($identifier, $password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            // Ustawienie danych w sesji
            $_SESSION['username'] = $identifier;
            $_SESSION['user'] = $identifier;

            header("Location: /index");
            exit();
        } else {
            $model['error'] = "Nieprawidłowe dane logowania.";
        }
    }
    return 'login';  // Zwrócenie widoku
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['user']);
    session_unset(); // Czyści dane sesji
    session_destroy(); // Niszczy sesję
    setcookie(session_name(), '', time() - 3600, '/'); // Usuwa ciasteczko sesji
    header("Location: /index"); // Przekierowanie na stronę główną
    exit();
}
