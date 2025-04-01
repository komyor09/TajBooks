<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "komyor", "11222033", "BookStore");

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if ($offset < 0 || $limit <= 0) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

function getPopularBooks($conn, $limit, $offset) {
    $sql = "SELECT * FROM books ORDER BY popularity DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }
    
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}

$books = getPopularBooks($conn, $limit, $offset);

echo json_encode(['books' => $books]);
?>
