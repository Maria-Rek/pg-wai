<?php
// require '../../vendor/autoload.php';
// require dirname(__DIR__, 2) . '/vendor/autoload.php';
require __DIR__ . '/../../vendor/autoload.php';



require_once '../business.php';
require_once '../dispatcher.php';
require_once '../routing.php';
require_once '../controllers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CZYSZCZENIE BAZY
function clear_db() {
    $db = get_db();
    $db->pictures->drop();
}

// clear_db();
$action = isset($_GET['action']) ? '/' . ltrim($_GET['action'], '/') : '/';

// Obsługa metod POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'upload') {
        upload();
        exit();
    }
}

// Sprawdzenie, czy akcja istnieje w routingu
if (isset($routing[$action])) {
    $function = $routing[$action];

    if (function_exists($function)) {
        // Sprawdzenie, czy funkcja wymaga argumentu
        $reflection = new ReflectionFunction($function);
        if ($reflection->getNumberOfParameters() > 0) {
            $model = [];
            $function($model); // Wywołanie funkcji z modelem
        } else {
            $function(); // Wywołanie funkcji bez argumentów
        }
    } else {
        // Jeśli brak funkcji, sprawdzamy, czy istnieje widok
        $view = "../views/$function.phtml";
        if (file_exists($view)) {
            require_once $view;
        } else {
            echo "404 Not Found: Widok lub funkcja '$function' nie istnieje.";
        }
    }
} else {
    echo "404 Not Found: Akcja '$action' nie została zdefiniowana w routingu.";
}
