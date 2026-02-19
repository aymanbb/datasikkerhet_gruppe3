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
                    'id' => $user['user_id'],
                    'username' => $username
                ];
                if ($_SESSION['guest'] == true) {
                    unset($_SESSION['guest']);
                    unset($_SESSION['permitted_subject']);
                }
                $_SESSION['logged_in'] = true;
                if ($user['is_teacher'] == true) {
                    $_SESSION['can_message'] = false;
                    $_SESSION['can_answer'] = true;
                    $subject = $db->findSubjectByLecturerId($user['user_id']);
                    try {
                        $params = http_build_query([
                            'ref' => $subject['subject_id']
                        ]);
                        header("Location: subject_messages.php?" . $params, true, 303);
                        exit;
                    } catch (PDOException $e) {
                        die("Serious error message for serious problems" . $e->getMessage());
                    }
                } else {
                    $_SESSION['can_message'] = true;
                    $_SESSION['can_answer'] = false;
                    header("Location: emneoversikt.php");
                    exit;
                }
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