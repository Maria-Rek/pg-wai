<?php

function watermark_picture($picture_path, $picture_info){
    $format = pathinfo($picture_path, PATHINFO_EXTENSION);
    $watermark = $picture_info['watermark'];
    $original_path = $picture_info['path'];
    if($format === 'png'){
        $image = imagecreatefrompng($original_path);
    }
    else if($format === 'jpg'){
        $image = imagecreatefromjpeg($original_path);
    }
    $fontSize = 5;
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $x = 20;
    $y = 30;
    imagestring($image, $fontSize, $x, $y, $watermark, $textColor); //NAKŁADA NA OBRAZ
    if($format === 'png'){
        imagepng($image, './images/watermark/' . $picture_info['id'] . '.png');
    }
    else if($format === 'jpg'){
        imagejpeg($image, './images/watermark/' . $picture_info['id'] . '.jpg');
    }
    imagedestroy($image); //ZWALNIA ZASOBY PAMIĘCI
}

function miniaturka($picture_path, $picture_info){
    $format = pathinfo($picture_path, PATHINFO_EXTENSION);
    $newHeight = 125;
    $newWidth = 200;
    $original_path = $picture_info['path'];
    if($format === 'png'){
        $image = imagecreatefrompng($original_path);
    }
    else if($format === 'jpg'){
        $image = imagecreatefromjpeg($original_path);
    }
    $img_height = imagesy($image); //WYSOKOŚĆ PLIKU ORYG.
    $img_width = imagesx($image); //SZEROKOŚĆ PLIKU ORYG.
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $img_width, $img_height); //SKALUJE OBRAZ
    if($format === 'png'){
        imagepng($resizedImage, './images/miniaturki/' . $picture_info['id'] . '.png');
    }
    else if($format === 'jpg'){
        imagejpeg($resizedImage, './images/miniaturki/' . $picture_info['id'] . '.jpg');
    }
    imagedestroy($image);
    imagedestroy($resizedImage);
}