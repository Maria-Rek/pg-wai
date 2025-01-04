<?php
use MongoDB\BSON\ObjectID;
require dirname(__DIR__, 1) . '/vendor/autoload.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
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
