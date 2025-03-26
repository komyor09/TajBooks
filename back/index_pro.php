<?php
function getPopularBooks($conn) {
    $sql = "SELECT * FROM books ORDER BY popularity DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}
?>
