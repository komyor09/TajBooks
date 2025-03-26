<?php
session_start();
require_once "../config/db.php";

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['user_id']) ) { //|| $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Проверка, что ID заказа передан
if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    die("ID заказа не передан.");
}

$order_id = $_POST['order_id'];

// Удаление заказа из базы данных
try {
    // Подготовка SQL-запроса для удаления заказа
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);

    // Перенаправление на страницу заказов после успешного удаления
    header("Location: orders.php");
    exit();
} catch (PDOException $e) {
    // Ошибка при удалении
    $error = "Ошибка удаления заказа: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
