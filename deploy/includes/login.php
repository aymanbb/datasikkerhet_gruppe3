<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';

class Login {

    public function login(string $name, string $pass) {
        $username = $name;
        $password = $pass;

        $db = new Database();

        if (empty($username) || empty($password)) {
            $message = "All fields are required.";
        } else {
            $user = $db->userFindByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                header("Location: success.php");
                exit;
            } else {
                $_SESSION['login_error'] = "Invalid username or password.";
            }
        }
    }
}
?>