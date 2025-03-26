<?php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['message'] = 'Вы успешно вышли!';
header('Location: ../index.php');
exit();
?>
