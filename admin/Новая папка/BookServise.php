<?php

include 'db_connection.php';
include 'Book.php';

if(isset($_POST['id']) && isset($_POST['action']) && $_POST['action']=='update')
{
    $bookId = (int) $_POST['id'];
    $title= $_POST['title'];
    $author= $_POST['author'];
    $price= $_POST['price'];
    $description= $_POST['description'];
    $genre= $_POST['genre'];
    // $created_at= $_POST['created_at'];
    $image = $_FILES['image'];

    $book = new Book();
    $result = $book->updateBook($bookId, $title, $author, $price, $description, $genre, $image);
    // echo $result;
    header('Location: read_book.php');
}elseif(isset($_POST['id']) && isset($_POST['action']) && $_POST['action']=='delete')
{
    $bookId = (int) $_POST['id'];
    
    $book = new Book();
    $result = $book->delete($bookId);
    // echo $result;
    header('Location: read_book.php');
} else {
    echo "ID книги не передан.";

}
