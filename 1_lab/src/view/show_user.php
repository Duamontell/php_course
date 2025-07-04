<?php

require_once __DIR__ . "/../store/UserTable.php";
require_once __DIR__ . "/../../index.php";

if ($userId = $_GET['user_id']) {
    $userTable = $userController->getUserTable();
    $con = $userTable->getPDO();
    $user = $userTable->findUserInDatabase($con, $userId);
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
    <p>Имя: <?= htmlspecialchars($user->getFirstName()) ?></p>
    <p>Фамилия: <?= htmlspecialchars($user->getLastName()) ?></p>
    <p>Отчество: <?= htmlspecialchars($user->getMiddleName()) ?></p>
    <p>Пол: <?= htmlspecialchars($user->getGender()) ?></p>
    <p>Дата рождения: <?= htmlspecialchars($user->getBirthDate()) ?></p>
    <p>Email: <?= htmlspecialchars($user->getEmail()) ?></p>
    <p>Телефон: <?= htmlspecialchars($user->getPhone()) ?></p>
    <img src="<?= htmlspecialchars($user->getAvatarPath()) ?>" alt="Аватар">
</body>

</html>