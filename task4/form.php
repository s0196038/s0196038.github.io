<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

session_start();

// Обрабатываем GET параметры для сообщений об ошибках
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('process.php');
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Получаем ошибки из cookies если есть
    $errors = [];
    if (isset($_COOKIE['form_errors']) && !empty($_COOKIE['form_errors'])) {
        $errors = json_decode($_COOKIE['form_errors'], true);
        // Удаляем cookies с ошибками после прочтения
        setcookie('form_errors', '', time() - 3600, '/');
    }
    
    // Получаем сохраненные значения из cookies (на год)
    $savedData = [];
    if (isset($_COOKIE['saved_form_data']) && !empty($_COOKIE['saved_form_data'])) {
        $savedData = json_decode($_COOKIE['saved_form_data'], true);
    }
    
    $showSuccess = !empty($_GET['save']);
    include('index.php');
}
?>