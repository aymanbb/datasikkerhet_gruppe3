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
            // avoid reusing $user for two meanings; call DB row $dbUser
            $dbUser = $db->userFindByUsername($username);
            if ($dbUser && password_verify($password, $dbUser['password'])) {
                session_regenerate_id(true);

                // store the keys subject_messages.php expects
                $_SESSION['user'] = [
                    'id' => $dbUser['user_id'],
                    'username' => $dbUser['username'] ?? $username,
                    // ensure is_teacher is present and boolean/int
                    //'is_teacher' => !empty($dbUser['is_teacher']) ? 1 : 0
                ];

                if (isset($_SESSION['guest'])) {
                    unset($_SESSION['guest']);
                    unset($_SESSION['permitted_subject']);
                }

                $_SESSION['logged_in'] = true;

                // explicit teacher check based on the normalized session field
                if ($_SESSION['user']['is_teacher'] == 1) {
                    $_SESSION['can_message'] = false;
                    $_SESSION['can_answer']  = true;

                    $subject = $db->findSubjectByLecturerId($dbUser['user_id']);
                    try {
                        $params = http_build_query([
                            'ref' => $subject['subject_id']
                        ]);
                        header("Location: subject_messages.php?" . $params, true, 303);
                        exit;
                    } catch (PDOException $e) {
                        die("Serious error message for serious problems");
                    }
                } else {
                    // student path
                    $_SESSION['can_message'] = true;
                    $_SESSION['can_answer']  = false;
                    header("Location: emneoversikt.php");
                    exit;
                }
            } else {
                // keep attempts and error in session as you did
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
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
            $dbUser = $db->userFindByUsername($username);
            if ($dbUser && password_verify($password, $dbUser['password'])) {
                session_regenerate_id(true);
                // set same session structure used elsewhere
                $_SESSION['user'] = [
                    'id' => $dbUser['user_id'] ?? $dbUser['id'] ?? null,
                    'username' => $dbUser['username'] ?? $username,
                    'is_teacher' => !empty($dbUser['is_teacher']) ? 1 : 0
                ];
                $_SESSION['logged_in'] = true;
                return true;
            } else {
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
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