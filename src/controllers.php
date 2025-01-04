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
        'repeat_password' => null,
        'error' => null  // Zmienione z `errors[]` na jeden komunikat `error`
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['nickname']) && !empty($_POST['password']) && !empty($_POST['repeat_password']) && !empty($_POST['email'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user = [
                'username' => $_POST['nickname'],
                'email' => $_POST['email'],
                'password' => $hash
            ];

            // Sprawdzenie zgodności haseł
            if ($_POST['password'] !== $_POST['repeat_password']) {
                $user['error'] = 'Hasła nie pasują.';
                $model['user'] = $user;
                return 'register.phtml';
            }

            // Sprawdzenie, czy login jest zajęty
            if (check_user_nickname($user)) {
                $user['error'] = 'Login jest już zajęty.';
                $model['user'] = $user;
                return 'register.phtml';
            }

            // Zapis użytkownika do bazy
            if (save_user(null, $user)) {
                header("Location: /index");  // Przekierowanie po sukcesie
                exit();
            } else {
                $user['error'] = 'Błąd zapisu do bazy danych.';
            }
        } else {
            $user['error'] = 'Wszystkie pola są wymagane.';
        }
    }

    $model['user'] = $user;
    return 'register.phtml';  // Zwracamy widok rejestracji
}
