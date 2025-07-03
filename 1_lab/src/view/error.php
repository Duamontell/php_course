<?php

$message = $_GET['msg'] ?? 'Неизвестная ошибка';

?>

<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
</head>

<body>
    <div>
        <h1>Ошибка!</h1>
        <p>
            <?php echo ($message); ?>
        </p>
        <a href="register_user.php">Вернуться назад</a>
    </div>
</body>

</html>