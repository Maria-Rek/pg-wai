<?php
use MongoDB\BSON\ObjectID;
require '../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->wai;

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
            return 'redirect:/galeria';
        }
        return 'redirect:/galeria';

}

function connectToDatabase() {
    $client = new MongoDB\Client(
        "mongodb://localhost:27017/wai",
        [
            'username' => 'wai_web',
            'password' => 'w@i_w3b',
        ]);
    return $client->wai;
}        



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

function getUserDataFromSession() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username']
        ];
    }
    return null; // Brak sesji użytkownika
}

// Funkcja rejestracji użytkownika
function registerUser($username, $email, $password) {
    global $db;
    $collection = $db->users;

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

// Funkcja logowania użytkownika
function loginUser($username, $password) {
    global $db;
    $collection = $db->users;

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
    session_regenerate_id(true);
    $_SESSION['user_id'] = (string)$user['_id'];
    $_SESSION['username'] = $user['username'];

    return true;
}

function checkUserSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();  // Rozpocznij sesję, jeśli jej nie ma
    }

    // Sprawdzenie, czy użytkownik jest zalogowany:
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header("Location: /index");  // Jeśli nie ma sesji, przekieruj na stronę główną
        exit();
    }

    // Zwracamy dane użytkownika, jeśli wszystko jest OK:
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ];
}
