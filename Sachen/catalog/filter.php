<?php
require '../config/db.php'; 

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$publisher = $_POST['publisher'] ?? '';
$format = $_POST['format'] ?? '';
$min_price = $_POST['min_price'] ?? '';
$max_price = $_POST['max_price'] ?? '';
$min_year = $_POST['min_year'] ?? '';
$max_year = $_POST['max_year'] ?? '';
$rating = $_POST['rating'] ?? '';
$availability = $_POST['availability'] ?? '';
$genres = $_POST['genre'] ?? [];
$languages = $_POST['language'] ?? [];
$sort = $_POST['sort'] ?? '';

$query = "SELECT * FROM books WHERE 1";
$params = [];

if ($title) {
    $query .= " AND title LIKE ?";
    $params[] = "%$title%";
}
if ($author) {
    $query .= " AND author LIKE ?";
    $params[] = "%$author%";
}
if ($publisher) {
    $query .= " AND publisher LIKE ?";
    $params[] = "%$publisher%";
}
if ($format) {
    $query .= " AND format = ?";
    $params[] = $format;
}
if ($min_price) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
}
if ($min_year) {
    $query .= " AND year >= ?";
    $params[] = $min_year;
}
if ($max_year) {
    $query .= " AND year <= ?";
    $params[] = $max_year;
}
if ($rating) {
    $query .= " AND rating >= ?";
    $params[] = $rating;
}
if ($availability) {
    $query .= " AND availability = ?";
    $params[] = $availability;
}

if (!empty($genres)) {
    $placeholders = implode(',', array_fill(0, count($genres), '?'));
    $query .= " AND genre IN ($placeholders)";
    $params = array_merge($params, $genres);
}

if (!empty($languages)) {
    $placeholders = implode(',', array_fill(0, count($languages), '?'));
    $query .= " AND language IN ($placeholders)";
    $params = array_merge($params, $languages);
}

$sortOptions = [
    'new' => 'year DESC',
    'cheap' => 'price ASC',
    'expensive' => 'price DESC',
    'popular' => 'rating DESC'
];
if ($sort && isset($sortOptions[$sort])) {
    $query .= " ORDER BY " . $sortOptions[$sort];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

foreach ($books as $book) {
    echo "<div>{$book['title']} - {$book['price']} TJS</div>";
}
?>
