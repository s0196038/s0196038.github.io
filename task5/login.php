<?php
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку
header('Content-Type: text/html; charset=UTF-8');

// Параметры подключения к БД
$user = 'u82287';
$pass = '9387760';
$dbname = 'u82287';

// Запускаем сессию
session_start();

// Если пользователь уже авторизован - перенаправляем на форму
if (!empty($_SESSION['login'])) {
    header('Location: form.php');
    exit();
}

// Обработка GET запроса - показываем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для изменения данных</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #d1e9f2;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-form {
            background: white;
            max-width: 400px;
            width: 100%;
            padding: 35px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #1a5f7a;
            font-size: 24px;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #0a4c63;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #bddce8;
            font-size: 14px;
            border-radius: 4px;
        }
        button {
            background: #2c8bb3;
            color: white;
            border: none;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background: #1a6f91;
        }
        .error-message {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .info {
            background-color: #d4edda;
            border: 1px solid #b8e0c2;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h1>Вход для изменения данных</h1>
        
        <?php if (!empty($_GET['error'])): ?>
            <div class="error-message">
                Неверный логин или пароль. Попробуйте снова.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($_GET['registered'])): ?>
            <div class="info">
                Ваши данные успешно сохранены! Войдите с полученными логином и паролем для их изменения.
            </div>
        <?php endif; ?>
        
        <form action="" method="post">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="login" required autocomplete="off" />
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="pass" required />
            </div>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
<?php
}
// Обработка POST запроса - проверка логина и пароля
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['pass'] ?? '');
    
    if (empty($login) || empty($password)) {
        header('Location: login.php?error=1');
        exit();
    }
    
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Ищем пользователя по логину
        $stmt = $db->prepare("SELECT id, login, password_hash FROM users WHERE login = :login");
        $stmt->execute([':login' => $login]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Проверяем пароль (сравниваем md5)
        if ($userData && md5($password) === $userData['password_hash']) {
            // Авторизация успешна
            $_SESSION['login'] = $userData['login'];
            $_SESSION['uid'] = $userData['id'];
            
            // Перенаправляем на главную страницу
            header('Location: form.php');
            exit();
        } else {
            // Неверный логин или пароль
            header('Location: login.php?error=1');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: login.php?error=1');
        exit();
    }
}