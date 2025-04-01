<?php
// Контроллер UserController.php
class UserController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function register($username, $email, $password) {
        $this->user->username = $username;
        $this->user->email = $email;
        $this->user->password = $password;
        return $this->user->register();
    }

    public function login($email, $password) {
        $this->user->email = $email;
        $this->user->password = $password;
        return $this->user->login();
    }
}
?>