<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Параметры подключения к БД
$userDB = 'u82287';
$passDB = '9387760';
$dbname = 'u82287';

// Функция для сохранения ошибок в cookies
function saveErrorsToCookie($errors) {
    setcookie('form_errors', json_encode($errors), time() + 3600, '/');
}

// Функция для сохранения данных в cookies на год
function saveDataToCookie($data) {
    setcookie('saved_form_data', json_encode($data), time() + 365 * 24 * 3600, '/');
}

// Функция генерации логина
function generateLogin($fio) {
    // Генерируем 6 случайных латинских букв
    $letters = 'abcdefghijklmnopqrstuvwxyz';
    $randomLetters = '';
    for ($i = 0; $i < 6; $i++) {
        $randomLetters .= $letters[rand(0, strlen($letters) - 1)];
    }
    
    // Добавляем underscore и две случайные цифры
    $login = $randomLetters . '_' . rand(10, 99);
    
    return $login;
}

// Функция генерации пароля
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

$errors = [];
$allowedLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];

// Сохраняем все введенные данные для возможного возврата в форму
$submittedData = [
    'fio' => $_POST['fio'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'birthdate' => $_POST['birthdate'] ?? '',
    'gender' => $_POST['gender'] ?? '',
    'languages' => $_POST['languages'] ?? [],
    'bio' => $_POST['bio'] ?? '',
    'contract' => isset($_POST['contract'])
];

// Валидация ФИО
if (empty($_POST['fio'])) {
    $errors['fio'] = 'Поле ФИО обязательно для заполнения.';
} else {
    $fio = trim($_POST['fio']);
    if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $fio)) {
        $errors['fio'] = 'ФИО должно состоять только из букв (русских или латинских), пробелов и дефисов.';
    } elseif (strlen($fio) > 150) {
        $errors['fio'] = 'ФИО должно быть не длиннее 150 символов.';
    } elseif (strlen($fio) < 5) {
        $errors['fio'] = 'ФИО должно содержать минимум 5 символов.';
    }
}

// Валидация телефона
if (empty($_POST['phone'])) {
    $errors['phone'] = 'Поле Телефон обязательно для заполнения.';
} else {
    $phone = trim($_POST['phone']);
    if (!preg_match('/^[\+\d\s\-\(\)]{10,20}$/', $phone)) {
        $errors['phone'] = 'Телефон должен содержать от 10 до 20 символов. Допустимые символы: цифры, +, -, пробел, (, ).';
    } elseif (!preg_match('/\d/', $phone)) {
        $errors['phone'] = 'Телефон должен содержать хотя бы одну цифру.';
    }
}

// Валидация Email
if (empty($_POST['email'])) {
    $errors['email'] = 'Поле Email обязательно для заполнения.';
} else {
    $email = trim($_POST['email']);
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errors['email'] = 'Введите корректный email в формате: name@domain.com.';
    }
}

// Валидация даты рождения
if (empty($_POST['birthdate'])) {
    $errors['birthdate'] = 'Поле Дата рождения обязательно для заполнения.';
} else {
    $birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birthdate']);
    $today = new DateTime();
    $minAge = new DateTime('-150 years');
    $maxAge = new DateTime('-14 years');
    
    if (!$birthdate) {
        $errors['birthdate'] = 'Введите корректную дату в формате ГГГГ-ММ-ДД.';
    } elseif ($birthdate > $today) {
        $errors['birthdate'] = 'Дата рождения не может быть в будущем.';
    } elseif ($birthdate < $minAge) {
        $errors['birthdate'] = 'Дата рождения не может быть старше 150 лет.';
    } elseif ($birthdate > $maxAge) {
        $errors['birthdate'] = 'Возраст должен быть не менее 14 лет.';
    }
}

// Валидация пола
if (empty($_POST['gender'])) {
    $errors['gender'] = 'Пожалуйста, выберите ваш пол.';
} elseif (!in_array($_POST['gender'], ['male', 'female'])) {
    $errors['gender'] = 'Выбран недопустимый пол.';
}

// Валидация языков программирования
if (empty($_POST['languages'])) {
    $errors['languages'] = 'Пожалуйста, выберите хотя бы один язык программирования.';
} else {
    foreach ($_POST['languages'] as $lang) {
        if (!in_array($lang, $allowedLanguages)) {
            $errors['languages'] = 'Выбран недопустимый язык программирования.';
            break;
        }
    }
}

// Валидация биографии
if (empty($_POST['bio'])) {
    $errors['bio'] = 'Поле Биография обязательно для заполнения.';
} else {
    $bio = trim($_POST['bio']);
    if (strlen($bio) > 5000) {
        $errors['bio'] = 'Биография должна быть не длиннее 5000 символов.';
    } elseif (strlen($bio) < 10) {
        $errors['bio'] = 'Биография должна содержать минимум 10 символов.';
    }
}

// Валидация контракта
if (empty($_POST['contract'])) {
    $errors['contract'] = 'Необходимо подтвердить ознакомление с контрактом.';
}

// Если есть ошибки, сохраняем их в cookies и возвращаемся к форме
if (!empty($errors)) {
    saveErrorsToCookie($errors);
    $_SESSION['form_data'] = $submittedData;
    header('Location: form.php');
    exit();
}

// Если ошибок нет, подключаемся к базе данных
try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $userDB, $passDB, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $isAuthorized = !empty($_SESSION['login']) && !empty($_SESSION['uid']);
    $userId = null;
    
    if ($isAuthorized) {
        // Авторизованный пользователь - обновляем существующую запись
        $userId = $_SESSION['uid'];
        
        // Обновляем данные в таблице applications (создаем новую запись или обновляем?)
        // По условию - перезаписываем данные
        $stmt = $db->prepare("
            INSERT INTO applications (user_id, fio, phone, email, birthdate, gender, bio, contract_agreed) 
            VALUES (:user_id, :fio, :phone, :email, :birthdate, :gender, :bio, :contract)
            ON DUPLICATE KEY UPDATE 
            fio = VALUES(fio), phone = VALUES(phone), email = VALUES(email),
            birthdate = VALUES(birthdate), gender = VALUES(gender), bio = VALUES(bio),
            contract_agreed = VALUES(contract_agreed)
        ");
        
        // Для ON DUPLICATE KEY нужен уникальный ключ. Добавим UNIQUE KEY (user_id) в таблицу applications
        // Или просто удалим старую запись и вставим новую
        $deleteStmt = $db->prepare("DELETE FROM applications WHERE user_id = :user_id");
        $deleteStmt->execute([':user_id' => $userId]);
        
        $stmt = $db->prepare("
            INSERT INTO applications (user_id, fio, phone, email, birthdate, gender, bio, contract_agreed) 
            VALUES (:user_id, :fio, :phone, :email, :birthdate, :gender, :bio, :contract)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':fio' => trim($_POST['fio']),
            ':phone' => trim($_POST['phone']),
            ':email' => trim($_POST['email']),
            ':birthdate' => $_POST['birthdate'],
            ':gender' => $_POST['gender'],
            ':bio' => trim($_POST['bio']),
            ':contract' => isset($_POST['contract']) ? 1 : 0
        ]);
        
        $applicationId = $db->lastInsertId();
        
        // Вставка языков
        $appLangStmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (:app_id, :lang_id)");
        foreach ($_POST['languages'] as $lang) {
            $langStmt = $db->prepare("SELECT id FROM programming_languages WHERE name = :name");
            $langStmt->execute([':name' => $lang]);
            $langId = $langStmt->fetchColumn();
            if ($langId) {
                $appLangStmt->execute([':app_id' => $applicationId, ':lang_id' => $langId]);
            }
        }
        
        // Сохраняем данные в cookies тоже
        saveDataToCookie($submittedData);
        
        header('Location: form.php?save=1');
        exit();
        
    } else {
        // Новая запись - создаем пользователя и приложение
        $db->beginTransaction();
        
        // Генерируем логин и пароль
        $login = generateLogin($_POST['fio']);
        $password = generatePassword(8);
        $passwordHash = md5($password);
        
        // Проверяем уникальность логина, если занят - добавляем суффикс
        $checkStmt = $db->prepare("SELECT id FROM users WHERE login = :login");
        $checkStmt->execute([':login' => $login]);
        $suffix = 1;
        while ($checkStmt->fetch()) {
            $newLogin = $login . $suffix;
            $checkStmt->execute([':login' => $newLogin]);
            if (!$checkStmt->fetch()) {
                $login = $newLogin;
                break;
            }
            $suffix++;
        }
        
        // Создаем пользователя
        $userStmt = $db->prepare("INSERT INTO users (login, password_hash) VALUES (:login, :hash)");
        $userStmt->execute([
            ':login' => $login,
            ':hash' => $passwordHash
        ]);
        $userId = $db->lastInsertId();
        
        // Создаем заявку
        $appStmt = $db->prepare("
            INSERT INTO applications (user_id, fio, phone, email, birthdate, gender, bio, contract_agreed) 
            VALUES (:user_id, :fio, :phone, :email, :birthdate, :gender, :bio, :contract)
        ");
        $appStmt->execute([
            ':user_id' => $userId,
            ':fio' => trim($_POST['fio']),
            ':phone' => trim($_POST['phone']),
            ':email' => trim($_POST['email']),
            ':birthdate' => $_POST['birthdate'],
            ':gender' => $_POST['gender'],
            ':bio' => trim($_POST['bio']),
            ':contract' => isset($_POST['contract']) ? 1 : 0
        ]);
        $applicationId = $db->lastInsertId();
        
        // Вставка языков
        $appLangStmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (:app_id, :lang_id)");
        foreach ($_POST['languages'] as $lang) {
            $langStmt = $db->prepare("SELECT id FROM programming_languages WHERE name = :name");
            $langStmt->execute([':name' => $lang]);
            $langId = $langStmt->fetchColumn();
            if ($langId) {
                $appLangStmt->execute([':app_id' => $applicationId, ':lang_id' => $langId]);
            }
        }
        
        $db->commit();
        
        // Сохраняем логин и пароль в cookies для отображения
        setcookie('new_login', $login, time() + 3600, '/');
        setcookie('new_pass', $password, time() + 3600, '/');
        
        // Сохраняем данные в cookies
        saveDataToCookie($submittedData);
        
        // Очищаем временные данные
        unset($_SESSION['form_data']);
        
        // Перенаправление для отображения логина/пароля
        header('Location: form.php?save=1&new=1');
        exit();
    }
    
} catch (PDOException $e) {
    if (isset($db) && !is_null($db->inTransaction()) && $db->inTransaction()) {
        $db->rollBack();
    }
    $errors['database'] = 'Ошибка базы данных: ' . $e->getMessage();
    saveErrorsToCookie($errors);
    header('Location: form.php');
    exit();
}
?>