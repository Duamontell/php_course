<?php

if (empty($_GET["user_id"])) {
    $message = "404: Запрашиваемая страница не найдена";
    header("Location: " . "index.php?action=error&msg={$message}", true, 303);
    die();
}

$userId = (int) $_GET["user_id"];
$userTable = $this->getUserTable();
$con = $userTable->getPDO();
$user = $userTable->findUserInDatabase($con, $userId);

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
    <p>Аватар:</p>
    <img src="uploads/<?= htmlspecialchars($user->getAvatarPath()) ?>" alt="Аватар">

</html>