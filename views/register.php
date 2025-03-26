<?php
// Шаблон register.php

include '../config/database.php';
include '../app/User.php';
include '../app/UserController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new UserController($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($controller->register($_POST['username'], $_POST['email'], $_POST['password'])) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка регистрации!";
    }
}
?>
<form method="post">
    <input type="text" name="username" placeholder="Имя пользователя" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Зарегистрироваться</button>
</form>