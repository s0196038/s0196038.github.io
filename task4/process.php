<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Функция для сохранения ошибок в cookies
function saveErrorsToCookie($errors) {
    setcookie('form_errors', json_encode($errors), time() + 3600, '/');
}

// Функция для сохранения данных в cookies на год
function saveDataToCookie($data) {
    setcookie('saved_form_data', json_encode($data), time() + 365 * 24 * 3600, '/');
}

// Функция для сохранения успешно отправленных данных в cookies
function saveSuccessDataToCookie($data) {
    setcookie('saved_form_data', json_encode($data), time() + 365 * 24 * 3600, '/');
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

// Валидация ФИО (только буквы, пробелы, дефисы)
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

// Валидация телефона (цифры, +, -, пробелы, скобки)
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
        $errors['email'] = 'Введите корректный email в формате: name@domain.com. Допустимы буквы, цифры, точки, дефисы и знак @.';
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
    $errors['gender'] = 'Выбран недопустимый пол. Допустимые значения: male, female.';
}

// Валидация языков программирования
if (empty($_POST['languages'])) {
    $errors['languages'] = 'Пожалуйста, выберите хотя бы один язык программирования.';
} else {
    foreach ($_POST['languages'] as $lang) {
        if (!in_array($lang, $allowedLanguages)) {
            $errors['languages'] = 'Выбран недопустимый язык программирования. Допустимые языки: ' . implode(', ', $allowedLanguages);
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
    // Проверка на недопустимые символы в биографии
    if (!preg_match('/^[а-яА-ЯёЁa-zA-Z0-9\s\.,!?\-:;\'"]+$/u', $bio)) {
        $errors['bio'] = 'Биография может содержать только буквы, цифры, пробелы и знаки препинания (. , ! ? - : ; " \').';
    }
}

// Валидация контракта
if (empty($_POST['contract'])) {
    $errors['contract'] = 'Необходимо подтвердить ознакомление с контрактом.';
}

// Если есть ошибки, сохраняем их в cookies и возвращаемся к форме
if (!empty($errors)) {
    saveErrorsToCookie($errors);
    
    // Сохраняем введенные данные в сессию для возврата в форму
    $_SESSION['form_data'] = $submittedData;
    
    // Перенаправляем GET запросом для отображения формы с ошибками
    header('Location: form.php');
    exit();
}

// Если ошибок нет, подключаемся к базе данных
$user = 'u82287';
$pass = '9387760';
$dbname = 'u82287';

try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Начало транзакции
    $db->beginTransaction();

    // Вставка основной информации
    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthdate, gender, bio, contract_agreed) 
                          VALUES (:fio, :phone, :email, :birthdate, :gender, :bio, :contract)");
    $stmt->execute([
        ':fio' => trim($_POST['fio']),
        ':phone' => trim($_POST['phone']),
        ':email' => trim($_POST['email']),
        ':birthdate' => $_POST['birthdate'],
        ':gender' => $_POST['gender'],
        ':bio' => trim($_POST['bio']),
        ':contract' => isset($_POST['contract']) ? 1 : 0
    ]);

    // Получаем ID последней вставленной записи
    $applicationId = $db->lastInsertId();

    // Вставка языков программирования
    $langStmt = $db->prepare("SELECT id FROM programming_languages WHERE name = :name");
    $appLangStmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (:app_id, :lang_id)");
    
    foreach ($_POST['languages'] as $lang) {
        $langStmt->execute([':name' => $lang]);
        $langId = $langStmt->fetchColumn();
        
        if ($langId) {
            $appLangStmt->execute([
                ':app_id' => $applicationId,
                ':lang_id' => $langId
            ]);
        }
    }

    // Завершение транзакции
    $db->commit();
    
    // Сохраняем успешно отправленные данные в cookies на год
    saveSuccessDataToCookie($submittedData);
    
    // Очищаем временные данные сессии
    unset($_SESSION['form_data']);
    
    // Перенаправление с сообщением об успехе
    header('Location: form.php?save=1');
    exit();
    
} catch (PDOException $e) {
    // Откат транзакции в случае ошибки
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    // Сохраняем ошибку базы данных в cookies
    $errors['database'] = 'Ошибка базы данных: ' . $e->getMessage();
    saveErrorsToCookie($errors);
    
    header('Location: form.php');
    exit();
}
?>