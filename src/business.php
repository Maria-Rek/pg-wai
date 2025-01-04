<?php
use MongoDB\BSON\ObjectID;
require '../../vendor/autoload.php';
<<<<<<< HEAD
require '../controllers.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
logout();
=======
>>>>>>> parent of f888c1e (POPSUTA GALERIA I UPLOAD)

function get_db()
{
    $mongo = new MongoDB\Client(
        "mongodb://localhost:27017/wai",
        [
            'username' => 'wai_web',
            'password' => 'w@i_w3b',
        ]);

    $db = $mongo->wai;

    return $db;
}

function get_pictures()
{
    $db = get_db();
    return $db->pictures->find()->toArray();
}

function get_picture_by_id($id)
{
    $db = get_db();
    $picture = $db->pictures->findOne(['id' => $id]);
    return $picture;
}


function handle_image_upload(){
        //VALIDATION
        if($_FILES['file']['size'] > 1024 * 1024){
            return 'redirect:/upload?error=2';
        }

        //FINAL UPLOAD
        $targetDirectory = "./images/";
        $id = uniqid();
        if($_FILES['file']['type'] === 'image/png'){
            $targetFile = $targetDirectory . $id . '.png';
        }
        else if($_FILES['file']['type'] === 'image/jpeg'){
            $targetFile = $targetDirectory . $id . '.jpg';
        }
        else{
            return 'redirect:/upload?error=1';

        }


        if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)){
            $db = get_db();
            $picture_info = [
                'id' => $id,
                'tytul' => $_POST['tytul'],
                'autor' => $_POST['autor'],
                'path' => $targetFile,
                'watermark' => $_POST['watermark'],
            ];
            try {
                $db->pictures->insertOne($picture_info);
            } catch (Exception $e) {
                // Handle the error here
                return 'redirect:/upload?error=3';
            }
            miniaturka($targetFile, $picture_info);
            watermark_picture($targetFile, $picture_info);
            return 'redirect:/cats/galeria';
        }
        return 'redirect:/cats/galeria';

}

// function connectToDatabase() {
//     $client = new MongoDB\Client("mongodb://wai_web:w@i_w3b@localhost:27017");
//     return $client->wai;
// }
function connectToDatabase() {
<<<<<<< HEAD
    $client = new MongoDB\Client(
        "mongodb://localhost:27017/wai",
        [
            'username' => 'wai_web',
            'password' => 'w@i_w3b',
        ]);
    return $client->wai;
}  
=======
    try {
        $mongo = new MongoDB\Client(
            "mongodb://localhost:27017/wai",
            [
                'username' => 'wai_web',
                'password' => 'w@i_w3b',
            ]
        );
        // Test połączenia z bazą
        $db = $mongo->selectDatabase('wai');
        $db->listCollections();
        return $db;
    } catch (Exception $e) {
        die("Błąd połączenia z MongoDB: " . $e->getMessage());
    }
}
>>>>>>> parent of f888c1e (POPSUTA GALERIA I UPLOAD)



function userExists($username, $email) {
    $db = connectToDatabase();
    $collection = $db->users;
    $user = $collection->findOne([
        '$or' => [
            ['username' => $username],
            ['email' => $email]
        ]
    ]);
    return $user;
}

function saveUser($username, $email, $hashed_password) {
    $db = connectToDatabase();
    $collection = $db->users;
    $result = $collection->insertOne([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password
    ]);
    return $result->isAcknowledged();
}

function verifyUser($identifier, $password) {
    $db = connectToDatabase();
    $collection = $db->users;
    $user = $collection->findOne([
        '$or' => [
            ['username' => $identifier],
            ['email' => $identifier]
        ]
    ]);

    if ($user) {
        return password_verify($password, $user['password']);
    }

    return false;
}
<<<<<<< HEAD

// function getUserDataFromSession() {
//     if (isset($_SESSION['user_id'])) {
//         return [
//             'id' => $_SESSION['user_id'],
//             'username' => $_SESSION['username']
//         ];
//     }
//     return null; // Brak sesji użytkownika
// }

// // Funkcja rejestracji użytkownika
// function registerUser($username, $email, $password) {
//     global $db;
//     $collection = $db->users;

//     // Sprawdzenie, czy użytkownik już istnieje
//     $existingUser = $collection->findOne(['username' => $username]);
//     if ($existingUser) {
//         throw new Exception("Nazwa użytkownika jest już zajęta.");
//     }

//     // Hashowanie hasła
//     $passwordHash = password_hash($password, PASSWORD_DEFAULT);

//     // Dodanie użytkownika do bazy
//     $result = $collection->insertOne([
//         'username' => $username,
//         'email' => $email,
//         'password_hash' => $passwordHash
//     ]);

//     return $result->getInsertedId();
// }

// // Funkcja logowania użytkownika
// function loginUser($username, $password) {
//     global $db;
//     $collection = $db->users;

//     // Pobieranie użytkownika
//     $user = $collection->findOne(['username' => $username]);
//     if (!$user) {
//         throw new Exception("Nie znaleziono użytkownika.");
//     }

//     // Weryfikacja hasła
//     if (!password_verify($password, $user['password_hash'])) {
//         throw new Exception("Nieprawidłowe hasło.");
//     }

//     // Tworzenie sesji
//     session_regenerate_id(true);
//     $_SESSION['user_id'] = (string)$user['_id'];
//     $_SESSION['username'] = $user['username'];

//     return true;
// }

// function checkUserSession() {
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();  // Rozpocznij sesję, jeśli jej nie ma
//     }

//     // Sprawdzenie, czy użytkownik jest zalogowany:
//     if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
//         header("Location: /index");  // Jeśli nie ma sesji, przekieruj na stronę główną
//         exit();
//     }

//     // Zwracamy dane użytkownika, jeśli wszystko jest OK:
//     return [
//         'id' => $_SESSION['user_id'],
//         'username' => $_SESSION['username']
//     ];
// }

function getUserDataFromSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();  // Rozpocznij sesję, jeśli jej nie ma
    }
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ];
    }
    return null; // Brak sesji użytkownika
}
function registerUser($username, $email, $password) {
    global $db;
    $collection = $db->users;

    // Walidacja danych
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Nieprawidłowy adres e-mail.");
    }
    if (strlen($username) < 3) {
        throw new Exception("Nazwa użytkownika musi mieć co najmniej 3 znaki.");
    }

    // Sprawdzenie, czy użytkownik już istnieje
    $existingUser = $collection->findOne(['username' => $username]);
    if ($existingUser) {
        throw new Exception("Nazwa użytkownika jest już zajęta.");
    }

    // Hashowanie hasła
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Dodanie użytkownika do bazy
    $result = $collection->insertOne([
        'username' => $username,
        'email' => $email,
        'password_hash' => $passwordHash
    ]);

    return $result->getInsertedId();
}
function loginUser($username, $password) {
    global $db;
    $collection = $db->users;

    // Walidacja danych
    if (empty($username) || empty($password)) {
        throw new Exception("Wszystkie pola muszą być wypełnione.");
    }

    // Pobieranie użytkownika
    $user = $collection->findOne(['username' => $username]);
    if (!$user) {
        throw new Exception("Nie znaleziono użytkownika.");
    }

    // Weryfikacja hasła
    if (!password_verify($password, $user['password_hash'])) {
        throw new Exception("Nieprawidłowe hasło.");
    }

    // Tworzenie sesji
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (string)$user['_id'];
    $_SESSION['username'] = $user['username'];

    return true;
}
function checkUserSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();  // Rozpocznij sesję, jeśli jej nie ma
    }

    // Sprawdzenie, czy użytkownik jest zalogowany
    if (empty($_SESSION['user_id']) || empty($_SESSION['username'])) {
        header("Location: /index");  // Jeśli brak danych sesji, przekierowanie na stronę główną
        exit();
    }

    // Zwracamy dane użytkownika, jeśli wszystko jest OK
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ];
}
=======
>>>>>>> parent of f888c1e (POPSUTA GALERIA I UPLOAD)
