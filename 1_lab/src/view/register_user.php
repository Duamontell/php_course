<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Регистрация пользователя</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/src/view/register_user.css">
</head>

<body>
    <h1>Регистрация пользователя:</h1>
    <!-- Переименовать user.php в create_user.php -->
    <form action="../actions/create_user.php" method="POST">
        <div class="form-row">
            <label for="first_name-input">Имя:</label>
            <input type="text" id="first_name-input" name="first_name">
        </div>
        <div class="form-row">
            <label for="last_name-input">Фамилия:</label>
            <input type="text" id="last_name-input" name="last_name">
        </div>
        <div class="form-row">
            <label for="middle_name-input">Отчество:</label>
            <input type="text" id="middle_name-input" name="middle_name">
        </div>
        <div class="form-row">
            <label for="male_gender-input">Мужчина</label>
            <input type="radio" id="male_gender-input" name="gender" value="man" checked>
            <label for="woman_gender-input">Женщина</label>
            <input type="radio" id="woman_gender-input" name="gender" value="woman">
        </div>
        <div class="form-row">
            <label for="birth_date-input">Дата рождения</label>
            <input type="date" id="birth_date-input" name="birth_date">
        </div>
        <div class="form-row">
            <label for="email-input">Email</label>
            <input type="email" id="email-input" name="email">
        </div>
        <div class="form-row">
            <label for="phone-input">Телефон</label>
            <input type="tel" id="phone-input" name="phone">
        </div>
        <div class="form-row">
            <label for="avatar-input">Аватар</label>
            <input type="file" id="avatar-input" name="avatar">
        </div>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>

</html>