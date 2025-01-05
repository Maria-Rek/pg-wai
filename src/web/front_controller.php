<?php
require '../../vendor/autoload.php';
require_once '../dispatcher.php';
require_once '../routing.php';
require_once '../controllers.php';

session_start();

function clear_db() {
    try {
        $db = get_db();
        $db->pictures->drop();
    } catch (Exception $e) {
        echo "Błąd podczas czyszczenia bazy danych: " . $e->getMessage();
    }
}
//clear_db();

$action_url = $_GET['action'];
if(!isset($action_url)){
    $action_url = '/';
}
else if($_SERVER['REQUEST_METHOD'] === 'POST'){
    upload($model);
}
dispatch($routing, $action_url);