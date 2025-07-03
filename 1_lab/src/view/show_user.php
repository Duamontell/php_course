<?php

require_once __DIR__ . "/../store/UserTable.php";

if ($userId = $_GET['user_id']) {
    $userTable = new UserTable();
    $con = $userTable->connectDatabase();
    $userInfo = $userTable->findUserInDatabase($con, $userId);
} else {
    $message = "404\nЗапрашиваемая страница не найдена";
    $redirectUrl = "../view/error.php?msg=" . $message;
    header("Location: " . $redirectUrl, true, 303);
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Пользователь</title>
</head>

<body>
    <p>Имя: <?= htmlspecialchars($userInfo["first_name"]) ?></p>
    <p>Фамилия: <?= htmlspecialchars($userInfo["last_name"]) ?></p>
    <p>Отчество: <?= htmlspecialchars($userInfo["middle_name"]) ?></p>
    <p>Пол: <?= htmlspecialchars($userInfo["gender"]) ?></p>
    <p>Дата рождения: <?= htmlspecialchars($userInfo["birth_date"]) ?></p>
    <p>Email: <?= htmlspecialchars($userInfo["email"]) ?></p>
    <p>Телефон: <?= htmlspecialchars($userInfo["phone"]) ?></p>
    <img src="<?= htmlspecialchars($userInfo["avatar_path"]) ?>" alt="Аватар">
</body>

</html>