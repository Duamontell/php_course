<!DOCTYPE html>
<html lang="ru">

<head>
	<title>Админ-панель</title>
	<meta charset="UTF-8">
	<style>
		div {
			border: 4px solid;
			display: flex;
			margin-bottom: 3px;
			justify-content: space-between;
		}

		a {
			text-decoration: none;
			color: black;
		}
	</style>
</head>

<body>
	<h1>Список пользователей</h1>
	<?php if (empty($users)) : ?> /
		<p>Пользователи не найдены</p>
	<?php else : ?>
		<p>Формат отображения: ФИО</p>
		<?php foreach ($users as $user) : ?>
			<form action="?action=delete_user&user_id=<?= $user->getUserId() ?>" method="POST">
				<div>
					<a href="?action=profile&user_id=<?= $user->getUserId() ?>">
						<p>
							<?= htmlspecialchars(
								$user->getLastName() . " " .
								$user->getFirstName() . " " .
								($user->getMiddleName() ?? "")
							) ?>
						</p>
					</a>
					<button type="submit"> Удалить</button>
				</div>
			</form>
		<?php endforeach ?>
	<?php endif ?>
</body>

</html>