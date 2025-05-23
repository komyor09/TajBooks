<?php
require_once '../config/db.php';
session_start();

$message = '';
$message_type = '';

if (isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Вы уже авторизованы!";
    $_SESSION['message_type'] = "warning";
    header("Location: profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];

    $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE $column = :identifier");
    $stmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: /../index.php");
            exit();
        } else {
            $message = "Неверный пароль!";
            $message_type = "error";
        }
    } else {
        $message = "Пользователь не найден!";
        $message_type = "error";
        header('Location: /auth/register.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | TajBooks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <?php
        $role = $_SESSION['role'] ?? '';
        if ($role === 'admin') {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/admin.php');
        } else {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/client.php');
        }
        require_once($_SERVER['DOCUMENT_ROOT'] . '/navbars/iphone-notification.php');
    ?>

    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type === 'error' ? 'danger' : 'success' ?> text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="w-50 mx-auto p-4 border rounded shadow">
            <h2 class="text-center mb-4">Вход</h2>
            <label for="identifier">Email или логин:</label>
            <input type="text" id="identifier" name="identifier" class="form-control mb-3" required>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" class="form-control mb-3" required>

            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
    </div>

    <footer class="bg-dark text-white py-3 text-center mt-4">
        <p>&copy; 2025 TajBooks. Все права защищены.</p>
    </footer>
</body>
</html>
