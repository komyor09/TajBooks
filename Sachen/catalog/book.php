<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];

   
    header("Location: book_detail.php?id=$book_id");
    exit;
}
?>
