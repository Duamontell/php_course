<!DOCTYPE html>
<html lang="ru">

<head>
	<title>Регистрация пользователя</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="/public/register_user.css">
</head>

<body>
	<!-- Сделать ограничение на длину строки -->
	<h1>Регистрация пользователя:</h1>
	<form action="?action=register_user" enctype="multipart/form-data" method="POST">
		<div class="form-row">
			<label for="first_name-input">Имя:</label>
			<input type="text" id="first_name-input" name="first_name" maxlength="50" required>
		</div>
		<div class="form-row">
			<label for="last_name-input">Фамилия:</label>
			<input type="text" id="last_name-input" name="last_name" maxlength="50" required>
		</div>
		<div class="form-row">
			<label for="middle_name-input">Отчество:</label>
			<input type="text" id="middle_name-input" name="middle_name" maxlength="50">
		</div>
		<div class="form-row">
			<label for="male_gender-input">Мужчина</label>
			<input type="radio" id="male_gender-input" name="gender" value="man" checked>
			<label for="woman_gender-input">Женщина</label>
			<input type="radio" id="woman_gender-input" name="gender" value="woman">
		</div>
		<div class="form-row">
			<label for="birth_date-input">Дата рождения</label>
			<input type="date" id="birth_date-input" name="birth_date" required>
		</div>
		<div class="form-row">
			<label for="email-input">Email</label>
			<input type="email" id="email-input" name="email" maxlength="50" required>
		</div>
		<div class="form-row">
			<label for="phone-input">Телефон</label>
			<input type="tel" id="phone-input" name="phone" maxlength="20">
		</div>
		<div class="form-row">
			<label for="avatar-input">Аватар</label>
			<input type="file" id="avatar-input" accept=".png, .jpeg, .gif" name="avatar">
		</div>
		<button type="submit">Зарегистрироваться</button>
	</form>
	<form action="?action=admin_panel" position="absolute" bottom="0px" method="POST">
		<button type="submit">Админ панель</button>
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