<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';

class Login {

    public function login(string $user, string $pass) {
        $db = new Database();
        $username = $user;
        $password = $pass;

        if (empty($username) || empty($password)) {
            $message = "All fields are required.";
        } elseif (!isLockedOut()) {
            $user = $db->userFindByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $_SESSION['logged_in'] = true;
                header("Location: emneoversikt.php");
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['login_error'] = "Invalid username or password.";
                if (isLockedOut()) {
                    echo "You're locked out! Please wait a few minutes before trying again.";
                }
            }
        }
    }
     public function api_login(string $user, string $pass) {
        $db = new Database();
        $username = $user;
        $password = $pass;

        if (empty($username) || empty($password)) {
            $message = "All fields are required.";
        } elseif (!isLockedOut()) {
            $user = $db->userFindByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $_SESSION['logged_in'] = true;
                return true;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['login_error'] = "Invalid username or password.";
                if (isLockedOut()) {
                    echo "You're locked out! Please wait a few minutes before trying again.";
                }
            }
        }
        
        return false;
    }
}
?>