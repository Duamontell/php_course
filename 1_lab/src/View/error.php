<!DOCTYPE html>
<html>

<head>
	<title>Error</title>
</head>

<body>
	<div>
		<h1>Ошибка!</h1>
		<p>
			<?php echo ($message == "" ? "404: Запрашиваемая страница не найдена!" : $message); ?>
		</p>
		<a href="?action=registration_page">Вернуться назад</a>
	</div>
</body>

</html>