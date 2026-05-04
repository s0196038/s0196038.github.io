<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анкета</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h3>Регистрационная форма</h3>
    
    <form action="form.php" method="POST">
        <p>
            <label>ФИО: <input type="text" name="fio" size="40" required></label>
        </p>

        <p>
            <label>Телефон: <input type="tel" name="phone" placeholder="+7 123 456-78-90"></label>
        </p>

        <p>
            <label>E-mail: <input type="email" name="email"></label>
        </p>

        <p>
            <label>Дата рождения: <input type="date" name="birthdate"></label>
        </p>

        <p>Пол:
            <label><input type="radio" name="gender" value="male"> Мужской</label>
            <label><input type="radio" name="gender" value="female"> Женский</label>
        </p>

        <p>
            <label>Любимый язык программирования:<br>
                <select name="languages[]" multiple size="5">
                    <option>Pascal</option>
                    <option>C</option>
                    <option>C++</option>
                    <option>JavaScript</option>
                    <option>PHP</option>
                    <option>Python</option>
                    <option>Java</option>
                    <option>Haskel</option>
                    <option>Clojure</option>
                    <option>Prolog</option>
                    <option>Scala</option>
                    <option>Go</option>
                </select>
            </label><br>
        </p>

        <p>
            <label>Биография:<br>
                <textarea name="bio" rows="5" cols="45" placeholder="Расскажите о себе..."></textarea>
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="contract" value="1"> С контрактом ознакомлен(а)
            </label>
        </p>

        <p>
            <button type="submit">Сохранить</button>
        </p>
    </form>
</body>
</html>