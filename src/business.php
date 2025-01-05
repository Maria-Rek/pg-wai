<?php
use MongoDB\Client;

function get_db()
{
    try {
        $mongo = new Client(
            "mongodb://localhost:27017/wai",
            [
                'username' => 'wai_web',
                'password' => 'w@i_w3b',
            ]
        );

        $db = $mongo->wai;
        return $db;
    } catch (Exception $e) {
        die("Błąd połączenia z bazą danych.");
    }
}

function get_pictures()
{
    $db = get_db();
    try {
        return $db->pictures->find()->toArray();
    } catch (Exception $e) {
        echo "Błąd odczytu z bazy danych: " . $e->getMessage();
        return [];
    }
}

function get_picture_by_id($id)
{
    $db = get_db();
    try {
        return $db->pictures->findOne(['id' => $id]);
    } catch (Exception $e) {
        echo "Błąd pobierania zdjęcia: " . $e->getMessage();
        return null;
    }
}

function handle_image_upload() {
    if (empty($_POST['tytul']) || empty($_POST['autor']) || empty($_POST['watermark'])) {
        return 'redirect:/upload?error=4'; // BRAK OBOWIĄZKOWEGO POLA
    }

    if ($_FILES['file']['size'] > 1024 * 1024) {
        return 'redirect:/upload?error=2'; // PLIK ZA DUŻY
    }

    $targetDirectory = "./images/";
    if (!is_dir($targetDirectory) || !is_writable($targetDirectory)) {
        return 'redirect:/upload?error=5'; // ATALOG NIE ISTNIEJE LUB NIE MA UPRAWNIEŃ
    }

    $id = uniqid();
    if ($_FILES['file']['type'] === 'image/png') {
        $targetFile = $targetDirectory . $id . '.png';
    } else if ($_FILES['file']['type'] === 'image/jpeg') {
        $targetFile = $targetDirectory . $id . '.jpg';
    } else {
        return 'redirect:/upload?error=1'; // ZŁY FORMAT PLIKU
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
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
            return 'redirect:/upload?error=3'; // BŁĄD ZAPISU DO BAZY
        }
        miniaturka($targetFile, $picture_info);
        watermark_picture($targetFile, $picture_info);
        return 'redirect:/cats/galeria';
    }
    return 'redirect:/cats/galeria';
}

function userExists($username, $email) {
    $db = get_db();
    try {
        return $db->users->findOne([
            '$or' => [
                ['username' => $username],
                ['email' => $email]
            ]
        ]);
    } catch (Exception $e) {
        echo "Błąd sprawdzania użytkownika: " . $e->getMessage();
        return null;
    }
}

function saveUser($username, $email, $hashed_password) {
    $db = get_db();
    $collection = $db->users;
    try {
        $result = $collection->insertOne([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ]);
        return $result->isAcknowledged();
    } catch (Exception $e) {
        echo "Błąd zapisu użytkownika: " . $e->getMessage();
        return false;
    }
}

function verifyUser($identifier, $password) {
    $db = get_db();
    try {
        $user = $db->users->findOne([
            '$or' => [
                ['username' => $identifier],
                ['email' => $identifier]
            ]
        ]);
        if ($user) {
            return password_verify($password, $user['password']);
        }
        return false;
    } catch (Exception $e) {
        echo "Błąd logowania: " . $e->getMessage();
        return false;
    }
}