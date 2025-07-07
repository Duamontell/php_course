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
		<a href="?action=registration_page">Вернуться назад</a>
	</div>
</body>

</html>