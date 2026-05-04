<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

// Если форма отправлена (POST) — обрабатываем данные
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('process.php');
} else {
    // Если GET и есть параметр save=1 — показываем сообщение об успехе
    $showSuccess = isset($_GET['save']) && $_GET['save'] == 1;
    include('index.html');  // Показываем HTML-форму
}
?>