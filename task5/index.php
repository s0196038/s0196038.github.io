<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма анкеты</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-field {
            border-color: #d9534f !important;
            background-color: #ffe6e6 !important;
        }
        
        .error-message {
            color: #d9534f;
            font-size: 13px;
            margin-top: 5px;
            font-weight: normal;
        }
        
        .general-errors {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .credentials-message {
            background-color: #d4edda;
            border: 1px solid #b8e0c2;
            color: #155724;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .user-info {
            background-color: #e8f4f8;
            border: 1px solid #b8d9e8;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logout-btn {
            background-color: #d9534f;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background-color: #c9302c;
        }
        
        .edit-info {
            background-color: #d9edf7;
            color: #31708f;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Анкета</h1>
    <?php
    // Если переменная $isAuthorized не определена, определяем ее
    if (!isset($isAuthorized)) {
        $isAuthorized = !empty($_SESSION['login']) && !empty($_SESSION['uid']);
    }
    ?>

    <?php if (!$isAuthorized): ?>
    <div style="text-align: right; margin-bottom: 20px;">
        <a href="login.php" style="color: #2c8bb3;">Войти для изменения данных</a>
    </div>
    <?php endif; ?>
    
    <?php
    // Получаем данные из сессии для возврата в форму (при ошибках)
    if (isset($_SESSION['form_data']) && !empty($_SESSION['form_data'])) {
        $formData = $_SESSION['form_data'];
        unset($_SESSION['form_data']);
    } elseif (!empty($savedData)) {
        $formData = $savedData;
    } else {
        $formData = [];
    }
    ?>
    
    <?php if (!empty($newCredentials)): ?>
    <div class="credentials-message">
        <strong>Ваши данные успешно сохранены!</strong><br>
        Логин для изменения данных: <strong><?= htmlspecialchars($newCredentials['login']) ?></strong><br>
        Пароль: <strong><?= htmlspecialchars($newCredentials['pass']) ?></strong><br>
        <em style="font-size: 13px;">Сохраните эти данные. Они понадобятся для изменения анкеты.</em>
    </div>
    <?php endif; ?>
    
    <?php if ($isAuthorized): ?>
    <div class="user-info">
        <span>Вы вошли как <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></span>
        <a href="logout.php" class="logout-btn">Выйти</a>
    </div>
    <div class="edit-info">
        Вы авторизованы. Ваши данные загружены из базы. Вы можете их изменить.
    </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    <div class="general-errors">
        <strong>Пожалуйста, исправьте следующие ошибки:</strong>
        <ul style="margin-top: 10px; margin-left: 20px;">
            <?php foreach ($errors as $field => $errorMsg): ?>
                <li><?= htmlspecialchars($errorMsg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form action="process.php" method="POST">
        <div class="form-group">
            <label for="fio">ФИО:</label>
            <input type="text" id="fio" name="fio" 
                   value="<?= htmlspecialchars($formData['fio'] ?? '') ?>"
                   class="<?= isset($errors['fio']) ? 'error-field' : '' ?>"
                   required>
            <?php if (isset($errors['fio'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['fio']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="tel" id="phone" name="phone" 
                   value="<?= htmlspecialchars($formData['phone'] ?? '') ?>"
                   class="<?= isset($errors['phone']) ? 'error-field' : '' ?>"
                   required>
            <?php if (isset($errors['phone'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['phone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                   class="<?= isset($errors['email']) ? 'error-field' : '' ?>"
                   required>
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="birthdate">Дата рождения:</label>
            <input type="date" id="birthdate" name="birthdate" 
                   value="<?= htmlspecialchars($formData['birthdate'] ?? '') ?>"
                   class="<?= isset($errors['birthdate']) ? 'error-field' : '' ?>"
                   required>
            <?php if (isset($errors['birthdate'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['birthdate']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Пол:</label>
            <div class="radio-group">
                <input type="radio" id="male" name="gender" value="male" 
                       <?= (isset($formData['gender']) && $formData['gender'] == 'male') ? 'checked' : '' ?>
                       class="<?= isset($errors['gender']) ? 'error-field' : '' ?>"
                       required>
                <label for="male">Мужской</label>
            </div>
            <div class="radio-group">
                <input type="radio" id="female" name="gender" value="female"
                       <?= (isset($formData['gender']) && $formData['gender'] == 'female') ? 'checked' : '' ?>
                       class="<?= isset($errors['gender']) ? 'error-field' : '' ?>">
                <label for="female">Женский</label>
            </div>
            <?php if (isset($errors['gender'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['gender']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="languages">Любимый(ые) язык(и) программирования:</label>
            <select id="languages" name="languages[]" multiple="multiple" 
                    class="<?= isset($errors['languages']) ? 'error-field' : '' ?>"
                    required>
                <option value="Pascal" <?= (isset($formData['languages']) && in_array('Pascal', (array)$formData['languages'])) ? 'selected' : '' ?>>Pascal</option>
                <option value="C" <?= (isset($formData['languages']) && in_array('C', (array)$formData['languages'])) ? 'selected' : '' ?>>C</option>
                <option value="C++" <?= (isset($formData['languages']) && in_array('C++', (array)$formData['languages'])) ? 'selected' : '' ?>>C++</option>
                <option value="JavaScript" <?= (isset($formData['languages']) && in_array('JavaScript', (array)$formData['languages'])) ? 'selected' : '' ?>>JavaScript</option>
                <option value="PHP" <?= (isset($formData['languages']) && in_array('PHP', (array)$formData['languages'])) ? 'selected' : '' ?>>PHP</option>
                <option value="Python" <?= (isset($formData['languages']) && in_array('Python', (array)$formData['languages'])) ? 'selected' : '' ?>>Python</option>
                <option value="Java" <?= (isset($formData['languages']) && in_array('Java', (array)$formData['languages'])) ? 'selected' : '' ?>>Java</option>
                <option value="Haskel" <?= (isset($formData['languages']) && in_array('Haskel', (array)$formData['languages'])) ? 'selected' : '' ?>>Haskel</option>
                <option value="Clojure" <?= (isset($formData['languages']) && in_array('Clojure', (array)$formData['languages'])) ? 'selected' : '' ?>>Clojure</option>
                <option value="Prolog" <?= (isset($formData['languages']) && in_array('Prolog', (array)$formData['languages'])) ? 'selected' : '' ?>>Prolog</option>
                <option value="Scala" <?= (isset($formData['languages']) && in_array('Scala', (array)$formData['languages'])) ? 'selected' : '' ?>>Scala</option>
                <option value="Go" <?= (isset($formData['languages']) && in_array('Go', (array)$formData['languages'])) ? 'selected' : '' ?>>Go</option>
            </select>
            <?php if (isset($errors['languages'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['languages']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="bio">Биография:</label>
            <textarea id="bio" name="bio" 
                      class="<?= isset($errors['bio']) ? 'error-field' : '' ?>"
                      required><?= htmlspecialchars($formData['bio'] ?? '') ?></textarea>
            <?php if (isset($errors['bio'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['bio']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="contract" name="contract" 
                       <?= (isset($formData['contract']) && $formData['contract'] === true) ? 'checked' : '' ?>
                       class="<?= isset($errors['contract']) ? 'error-field' : '' ?>"
                       required>
                <label for="contract">С контрактом ознакомлен</label>
            </div>
            <?php if (isset($errors['contract'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['contract']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Сохранить</button>
        
        <?php if (isset($showSuccess) && $showSuccess && empty($newCredentials)): ?>
            <div class="success-message">
                Спасибо за заполнение формы! Ваши данные успешно сохранены.
            </div>
        <?php endif; ?>
    </form>
</body>
</html>