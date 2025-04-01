<?php
// Шаблон login.php
include '../config/database.php';
include '../app/User.php';
include '../app/UserController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new UserController($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $controller->login($_POST['email'], $_POST['password']);
    if ($user) {
        session_start();
        $_SESSION['username'] = $user['username'];
        echo "Добро пожаловать, " . $_SESSION['username'];
        header("Location: ../index.php");
        exit;
    } else {
        echo "Неверные данные!";
    }
}
?>
<form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
</form>