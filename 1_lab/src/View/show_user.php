<?php

if (empty($_GET["user_id"])) {
	$this->redirectWithError("404: Запрашиваемая страница не найдена!");
}

$userId = (int) $_GET["user_id"];
$userTable = $this->getUserTable();
$con = $userTable->getPDO();
if (is_null($user = $userTable->findUserInDatabase($con, $userId))) {
	$this->redirectWithError("Такого пользователя не существует!");
}


?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Пользователь</title>
	<style>
		div {
			margin-top: 10px;
		}
	</style>
</head>

<body>
	<h1>Профиль пользователя</h1>
	<form action="?action=update&user_id=<?= $userId ?>" enctype="multipart/form-data" method="POST">
		<div>
			<label for="first_name-input">Имя: </label>
			<input type="text" id="first_name-input"
				value="<?= htmlspecialchars($user->getFirstName()) ?>"
				name="first_name" required>
		</div>
		<div>
			<label for="last_name-input">Фамилия:</label>
			<input type="text" id="last_name-input"
				value="<?= htmlspecialchars($user->getLastName()) ?>"
				name="last_name" required>
		</div>
		<div>
			<label for="middle_name-input">Отчество:</label>
			<input type="text" id="middle_name-input"
				value="<?= htmlspecialchars($user->getMiddleName()) ?>"
				name="middle_name">
		</div>
		<div>
			<label for="gender-select">Пол: </label>
			<select name="gender" id="gender-select">
				<option value="man"
					<?= $user->getGender() == "man" ? "selected" : "" ?>>
					Мужской
				</option>
				<option value="woman">
					<?= $user->getGender() == "woman" ? "selected" : "" ?>
					Женский
				</option>
			</select>
		</div>
		<div>
			<label for="birth_date-input">Дата рождения</label>
			<input type="date" id="birth_date-input"
				value="<?= htmlspecialchars($user->getBirthDate()) ?>"
				name="birth_date" required>
		</div>
		<div>
			<label for="email-input">Email</label>
			<input type="email" id="email-input"
				value="<?= htmlspecialchars($user->getEmail()) ?>"
				name="email" required>
		</div>
		<div>
			<label for="phone-input">Телефон</label>
			<input type="tel" id="phone-input"
				value="<?= htmlspecialchars($user->getPhone()) ?>"
				name="phone">
		</div>
		<div>
			<label for="avatar-input">Аватар</label>
			<input type="file" id="avatar-input" accept=".png, .jpeg, .gif" name="avatar">
		</div>
		<?php
		$avatar = $user->getAvatarPath();
		$avatarFile = __DIR__ . "/../../uploads/" . $avatar;
		if (file_exists($avatarFile)):
		?>
			<img
				src="uploads/<?= htmlspecialchars($avatar) ?>"
				width="100px"
				style="display: block;"
				alt="Аватар пользователя">
		<?php
		endif;
		?>
		<button type="submit">Обновить данные</button>
	</form>
	<script>
		document.getElementById("phone-input").addEventListener("keypress", function(e) {
			if (!/[0-9]/.test(e.key)) {
				e.preventDefault();
			}
		})
	</script>
</body>

</html>