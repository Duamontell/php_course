<?php

$message = $_GET['msg'] ?? 'Страница не найдена';

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
        <a href="index.php?action=registration">Вернуться назад</a>
    </div>
</body>

</html>