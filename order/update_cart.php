<?php
session_start();
require_once '../config/db.php';
require_once '../order/cartClass.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Метод не поддерживается');
}

if (!isset($_POST['book_id']) || !isset($_POST['action'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Неверные параметры запроса');
}

$book_id = (int)$_POST['book_id'];
echo $book_id;
$action = $_POST['action'];


$cart = new Cart($pdo, $_SESSION['user_id']);


$stmt = $pdo->prepare("
    SELECT b.*
    FROM book b
    WHERE b.id = ?
");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header('HTTP/1.1 404 Not Found');
    exit('Книга не найдена');
}

switch ($action) {
    case 'add':
        $cart->add_to_cart($book_id, 1);
        break;
        
    case 'increase':
        $cart->increase_quantity($book['id']);
        break;
        
    case 'decrease':
        $cart->decrease_quantity($book_id);
        break;
        
    case 'remove':
        $cart->remove_from_cart($book['id']);
        break;
        
    default:
        header('HTTP/1.1 400 Bad Request');
        exit('Неизвестное действие');
}

$referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: $referer");
exit();