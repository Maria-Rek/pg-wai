<?php
require_once("business.php");
require_once("image-utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
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

// function register() {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         $username = $_POST['username'] ?? '';
//         $email = $_POST['email'] ?? '';
//         $password = $_POST['password'] ?? '';

//         // Walidacja danych
//         if (strlen($username) < 3) {
//             echo "Nazwa użytkownika musi mieć co najmniej 3 znaki.";
//             return;
//         }
//         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//             echo "Nieprawidłowy format adresu e-mail.";
//             return;
//         }
//         if (strlen($password) < 6) {
//             echo "Hasło musi mieć co najmniej 6 znaków.";
//             return;
//         }

//         // Sprawdzenie, czy użytkownik istnieje
//         $existingUser = userExists($username, $email);
//         if ($existingUser) {
//             if ($existingUser['username'] === $username) {
//                 echo "Nazwa użytkownika jest zajęta.";
//             } elseif ($existingUser['email'] === $email) {
//                 echo "E-mail jest już zarejestrowany.";
//             }
//             return;
//         }

//         // Hashowanie hasła i zapis użytkownika
//         $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
//         if (saveUser($username, $email, $hashedPassword)) {
//             echo "Rejestracja zakończona pomyślnie!";
//         } else {
//             echo "Wystąpił błąd podczas rejestracji.";
//         }
//     } else {
//         require '../views/register.phtml';
//     }
// }
function register(&$model)
{
    $user = [
        'nickname' => null,
        'password' => null,
        'repeat_password' => null,
        '_id' => null,
        'error' => null
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['nickname']) && !empty($_POST['password']) && !empty($_POST['repeat_password'])) {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user = [
                'nickname' => $_POST['nickname'],
                'password' => $hash
            ];

            if ($_POST['password'] !== $_POST['repeat_password']) {
                $user['error'] .= 'Hasła nie pasują ';
                $model['user'] = $user;
                return 'register_view';
            }
            if (check_user_nickname($user)) {
                $user['error'] .= 'Login zajęty';
                $model['user'] = $user;
                return 'register_view';
            }
            if (save_user($id, $user)) {
                header("Location: /index"); // Przekierowanie na stronę główną po rejestracji
                exit();
            }
        }
    }

    $model['user'] = $user;
    return 'register_view';
}

function login(&$model) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        if (verifyUser($identifier, $password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();  // Rozpocznij sesję



            }
            $_SESSION['username'] = $identifier;  // Nazwa użytkownika
            $_SESSION['user'] = $identifier;  

            header("Location: /index");
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

    unset($_SESSION['user']);
    session_unset(); // Czyści dane sesji
    session_destroy(); // Niszczy sesję
    setcookie(session_name(), '', time() - 3600, '/'); // Usuwa ciasteczko sesji
    header("Location: /index"); // Przekierowanie na stronę główną
    exit();
}
