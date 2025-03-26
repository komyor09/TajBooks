<?php
session_start(); // Инициализация сессии

// Проверяем, если это POST запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем ID книги из формы
    $book_id = $_POST['book_id'];

    // Логика для обработки книги по ID
    // Например, можно запросить информацию о книге из базы данных по ID
    // Пример:
    // $book = getBookById($book_id);

    // Перенаправляем на страницу с деталями книги
    header("Location: book_detail.php?id=$book_id");
    exit;
}
?>
