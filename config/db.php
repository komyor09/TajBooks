<?php
$host = "localhost";
$dbname = "BookStore";
$username = "komyor";
$password = "11222033";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
    exit;
}
?>
