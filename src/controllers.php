<?php
require_once("business.php");
require_once("image-utils.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function upload(&$model) {
    checkUserSession();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        $result = handle_image_upload();
        $model['upload_result'] = $result;
        return '/upload';
    } else {
        return '/upload';
    }
}

function galeria(&$model) {
    checkUserSession();  // Sprawdzenie sesji
    global $db;
    $collection = $db->pictures;  // Powinno być "pictures", a nie "images"

    $images = $collection->find();  // Pobiera wszystkie zdjęcia
    $model['images'] = iterator_to_array($images);

    return '/galeria';  // Ścieżka do widoku galerii
}




function index(&$model) {
    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $model['user'] = $_SESSION['user']; // Przekazujemy dane użytkownika do widoku
        } else {
            $model['user'] = null; // Brak zalogowanego użytkownika
        }

        $view_path = '../views/index.phtml';
        if (!file_exists($view_path)) {
            throw new Exception("Brak pliku widoku: index.phtml");
        }

        require $view_path; // Wyświetlamy stronę główną
    } catch (Exception $e) {
        error_log("Błąd w funkcji index: " . $e->getMessage());
        echo "Wystąpił błąd podczas ładowania strony.";
    }
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

function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        if (verifyUser($identifier, $password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();  // Rozpocznij sesję
            }
            $_SESSION['user_id'] = '1';  // Tymczasowe ID (dla testu)
            $_SESSION['username'] = $identifier;  // Nazwa użytkownika

            header("Location: /upload");
            exit();
        } else {
            $model['error'] = "Nieprawidłowe dane logowania.";
        }
    }
    require '../views/login.phtml';  // Formularz logowania
}



function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset(); // Czyści dane sesji
    session_destroy(); // Niszczy sesję
    setcookie(session_name(), '', time() - 3600, '/'); // Usuwa ciasteczko sesji
    header("Location: /index"); // Przekierowanie na stronę główną
    exit();
}


