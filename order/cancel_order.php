<?php
session_start();
require "../config/db.php";

// Проверка, есть ли заказ в сессии
if (!isset($_SESSION['order_id'])) {
    echo "Нет данных для отмены заказа.";
    exit();
}

$order_id = $_SESSION['order_id'];

try {
    // Подготовка запроса на отмену заказа
    $query = "UPDATE Orders SET status = 'cancelled' WHERE id = :order_id";
    $stmt = $pdo->prepare($query);

    // Привязка параметра
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

    // Выполнение запроса
    $stmt->execute();

    // Проверка, был ли затронут хотя бы один ряд
    if ($stmt->rowCount() > 0) {
        echo "Заказ №$order_id был успешно отменен.";
        // Очистить сессию после успешной отмены заказа
        unset($_SESSION['order_id']);
        header('Location: ../order/orders.php');
    } else {
        echo "Ошибка при отмене заказа. Попробуйте снова.";
    }
} catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
}

// Закрытие соединения с БД
// В PDO соединение автоматически закрывается при уничтожении объекта, поэтому явное закрытие не нужно
?>
