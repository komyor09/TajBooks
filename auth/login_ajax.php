<?php
require_once '../config/db.php';
session_start();
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];

    $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE $column = :identifier");
    $stmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $response['status'] = 'success';
            $response['message'] = 'Вы успешно вошли!';
        } else {
            $response['message'] = 'Неверный пароль!';
        }
    } else {
        $response['message'] = 'Пользователь не найден!';
    }
}

echo json_encode($response);
?>
