<?php

declare(strict_types=1);

$userTable = $this->getUserTable();
$con = $userTable->getPDO();
$users = $userTable->grabAllUsers($con);

?>

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
					<p>
						<?= htmlspecialchars(
							$user->getLastName() . " " .
								$user->getFirstName() . " " .
								($user->getMiddleName() ?? "")
						) ?>
					</p>
					<button type="submit"> Удалить</button>
				</div>
			</form>
		<?php endforeach ?>
	<?php endif ?>
</body>

</html>