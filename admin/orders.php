<?php
session_start();
require_once "../config/db.php";

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['user_id']) ) { //|| $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Получаем список заказов из базы данных
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY createdAt DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Ошибка получения данных: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../admin/admin.css">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<header class="bg-dark text-white py-3">
    <nav class="container_1 d-flex justify-content-between align-items-center">
        <a href="../index.php" class="text-white d-flex align-items-center p-3">
            <img src="../pics/logo.jpg" alt="Logo" class="me-1" style="width: 50px;">
            <div class="row">
                <span class="h4 text-center">TajBooks</span>
                <span class="h6 text-center">Read Learn Grow</span>
            </div>
        </a>
        <ul class="nav ms-auto">
            <li class="nav-item ms-3">
                <a href="../catalog/catalog.php" class="nav-link text-white">
                    <i class="fas fa-book me-2"></i>Каталог
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="../order/cart.php" class="nav-link text-white">
                    <i class="fas fa-shopping-cart me-2"></i>Корзина
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="../admin/manage_books.php" class="nav-link text-white">
                    <i class="fas fa-cogs me-2"></i>Управление книгами
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="../admin/add_book.php" class="nav-link text-white">
                    <i class="fas fa-plus me-2"></i>Добавить книгу
                </a>
            </li>
            <li class="nav-item ms-3">
                <a href="../admin/orders.php" class="nav-link text-white">
                    <i class="fas fa-box me-2"></i>Заказы
                </a>
            </li>
            <li class="nav-item ms-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../auth/logout.php" class="nav-link text-white">
                        <i class="fas fa-sign-out-alt me-2"></i>Выйти
                    </a>
                <?php else: ?>
                    <a href="../auth/login.php" class="nav-link text-white">
                        <i class="fas fa-sign-in-alt me-2"></i>Войти / Регистрация
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>

<div class="container mt-5">
    <h2>Управление заказами</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Имя клиента</th>
                <th>Дата заказа</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['client_name']) ?></td>
                    <td><?= htmlspecialchars($order['createdAt']) ?></td>
                    <td>
                        <?php
                        switch ($order['status']) {
                            case 'pending': echo 'Ожидает'; break;
                            case 'shipped': echo 'Отправлен'; break;
                            case 'completed': echo 'Завершён'; break;
                            default: echo 'Неизвестен'; break;
                        }
                        ?>
                    </td>
                    <td>
                        <!-- Кнопка для изменения статуса заказа -->
                        <a href="edit_order.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Изменить статус
                        </a>
                        <!-- Кнопка для удаления заказа -->
                        <form action="delete_order.php" method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
