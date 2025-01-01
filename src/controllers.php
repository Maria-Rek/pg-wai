<?php
require_once("business.php");
require_once("image-utils.php");

function rats(&$model){
    return '/rats';
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
    $rats = get_pictures();
    $model['rats'] = $rats;
    return '/galeria';
}

function index(&$model){
    return '/index';
}

function yato(&$model){
    return '/rats/yato';
}

function ashe(&$model){
    return '/rats/ashe';
}

function akira(&$model){
    return '/rats/akira';
}

function viewimage(&$model)
{
    $model['id'] = $_GET['id'];
    return '/viewimage';
}