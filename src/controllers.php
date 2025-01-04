<?php
require_once("business.php");
require_once("image-utils.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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


// function login(&$model) {
//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             $identifier = $_POST['identifier'] ?? '';
//             $password = $_POST['password'] ?? '';
    
//             if (verifyUser($identifier, $password)) {
//                 if (session_status() === PHP_SESSION_NONE) {
//                     session_start();  // Rozpocznij sesję
//                 }
//                 $_SESSION['user_id'] = '1';  // Tymczasowe ID (dla testu)
//                 $_SESSION['username'] = $identifier;  // Nazwa użytkownika
    
//                 header("Location: /upload");
//                 exit();
            
//             } else {
//                 $model['error'] = "Nieprawidłowe dane logowania.";
//             }
//         require '../views/login.phtml';  // Formularz logowania
//     }
// }
    
    
    
//     function logout() {
//         if (session_status() === PHP_SESSION_NONE) {
//             session_start();
//         }
//         session_unset(); // Czyści dane sesji
//         session_destroy(); // Niszczy sesję
//         setcookie(session_name(), '', time() - 3600, '/'); // Usuwa ciasteczko sesji
//         header("Location: /index"); // Przekierowanie na stronę główną
//         exit();
//     }

function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = trim(filter_input(INPUT_POST, 'identifier', FILTER_SANITIZE_STRING));
        $password = trim($_POST['password']);

        try {
            if (verifyUser($identifier, $password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = uniqid();  // Prawdziwy identyfikator użytkownika
                $_SESSION['username'] = $identifier;

                header("Location: /upload");
                exit();
            } else {
                $model['error'] = "Nieprawidłowe dane logowania.";
            }
        } catch (Exception $e) {
            $model['error'] = "Błąd serwera: " . $e->getMessage();
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
    setcookie(session_name(), '', time() - 3600, '/', '', true, true);  // Usuwa ciasteczko sesji z zabezpieczeniami
    header("Location: /index"); // Przekierowanie na stronę główną
    exit();
}
