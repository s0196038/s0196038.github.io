<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

session_start();

// Параметры подключения к БД
$user = 'u82287';
$pass = '9387760';
$dbname = 'u82287';

// Определяем переменные, которые будут доступны в index.php
$isAuthorized = !empty($_SESSION['login']) && !empty($_SESSION['uid']);
$savedData = [];
$errors = [];
$cookieData = [];
$showSuccess = false;
$newCredentials = [];

// Если пользователь авторизован, загружаем его данные из БД
if ($isAuthorized) {
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Загружаем данные приложения пользователя
        $stmt = $db->prepare("SELECT * FROM applications WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
        $stmt->execute([':user_id' => $_SESSION['uid']]);
        $appData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($appData) {
            $savedData = [
                'fio' => $appData['fio'],
                'phone' => $appData['phone'],
                'email' => $appData['email'],
                'birthdate' => $appData['birthdate'],
                'gender' => $appData['gender'],
                'bio' => $appData['bio'],
                'contract' => (bool)$appData['contract_agreed']
            ];
            
            // Загружаем выбранные языки программирования
            $langStmt = $db->prepare("
                SELECT pl.name FROM application_languages al 
                JOIN programming_languages pl ON al.language_id = pl.id 
                WHERE al.application_id = :app_id
            ");
            $langStmt->execute([':app_id' => $appData['id']]);
            $savedData['languages'] = $langStmt->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (PDOException $e) {
        // Ошибка подключения к БД
    }
}

// Обрабатываем GET параметры для сообщений об ошибках
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('process.php');
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Получаем ошибки из cookies если есть
    if (isset($_COOKIE['form_errors']) && !empty($_COOKIE['form_errors'])) {
        $errors = json_decode($_COOKIE['form_errors'], true);
        setcookie('form_errors', '', time() - 3600, '/');
    }
    
    // Получаем сохраненные значения из cookies (для неавторизованных)
    if (isset($_COOKIE['saved_form_data']) && !empty($_COOKIE['saved_form_data'])) {
        $cookieData = json_decode($_COOKIE['saved_form_data'], true);
    }
    
    // Для авторизованных используем данные из БД, иначе из cookies
    $formData = $isAuthorized ? $savedData : $cookieData;
    
    $showSuccess = !empty($_GET['save']);
    
    // Если есть сообщение о новых логине/пароле
    if (isset($_COOKIE['new_login']) && isset($_COOKIE['new_pass'])) {
        $newCredentials = [
            'login' => strip_tags($_COOKIE['new_login']),
            'pass' => strip_tags($_COOKIE['new_pass'])
        ];
        setcookie('new_login', '', time() - 3600, '/');
        setcookie('new_pass', '', time() - 3600, '/');
    }
    
    include('index.php');
}
?>