<?php
session_start();
require_once "../config/db.php";

// Проверка на админа
if (!isset($_SESSION['user_id'])) { // || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID заказа не передан.");
}

$order_id = $_GET['id'];
$order = null;

// Получение данных заказа
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Ошибка получения данных: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    // Обновление статуса заказа
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $order_id]);
        
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка обновления статуса: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заказа</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Редактирование заказа #<?= htmlspecialchars($order['id']) ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="edit_order.php?id=<?= htmlspecialchars($order['id']) ?>" method="POST">
        <div class="mb-3">
            <label for="status" class="form-label">Статус заказа</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Ожидает</option>
                <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : '' ?>>Отправлен</option>
                <option value="completed" <?= ($order['status'] == 'completed') ? 'selected' : '' ?>>Завершён</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Сохранить изменения</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
