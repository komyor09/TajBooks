// logout.php (Выход из аккаунта)
<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php");
exit();
?>