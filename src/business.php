<?php
use MongoDB\BSON\ObjectID;

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
        //WALIDACJA
        if($_FILES['file']['size'] > 1024 * 1024){
            return 'redirect:/upload?error=2'; //PLIK ZA DUŻY
        }

        //UPLOAD
        $targetDirectory = "./images/";
        $id = uniqid();
        if($_FILES['file']['type'] === 'image/png'){
            $targetFile = $targetDirectory . $id . '.png';
        }
        else if($_FILES['file']['type'] === 'image/jpeg'){
            $targetFile = $targetDirectory . $id . '.jpg';
        }
        else{
            return 'redirect:/upload?error=1'; //ZŁY FORMAT PLIKU

        }

        //ZAPISANIE DO BAZY
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
                return 'redirect:/upload?error=3'; //BŁĄD ZAPISU DO BAZY
            }
            miniaturka($targetFile, $picture_info);
            watermark_picture($targetFile, $picture_info);
            return 'redirect:/cats/galeria';
        }
        return 'redirect:/cats/galeria';
}

function userExists($username, $email) {
    $db = get_db();
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
    $db = get_db();
    $collection = $db->users;
    $result = $collection->insertOne([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password
    ]);
    return $result->isAcknowledged();
}

function verifyUser($identifier, $password) {
    $db = get_db();
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

    return false;  //BRAK UŻYTKOWNIKA/NIEPOPRAWNE HASŁO
}