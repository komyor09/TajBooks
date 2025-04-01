<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id']) ) { 
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    die("ID заказа не передан.");
}

$order_id = $_POST['order_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $order_id]);

    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);


    header("Location: ../admin/orders.php");
    exit();
} catch (PDOException $e) {
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
