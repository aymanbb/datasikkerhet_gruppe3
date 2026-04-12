<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        echo "Please enter your email address.";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit;
    }

    try {
        $user = $db->userFindByEmail($email);

        if (!$user) {
            echo "No account found for that email.";
            exit;
        }

        // generate secure password (12 chars)
        $plain_password = substr(bin2hex(random_bytes(8)), 0, 12);

        $db->resetPassword($user['username'], $plain_password);

        echo "Here's the new password for " . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($plain_password, ENT_QUOTES, 'UTF-8');

    } catch (Exception $e) {
        error_log('Password reset error: ' . $e->getMessage());
        echo "Server error. Please try again.";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Forgot Password</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="guest_login.php">Fortsett som gjest</a></li>
            <li><a href="dokumentasjon.html">Dokumentasjon</a></li>
        </ul>
    </nav>
    <article>
        <h1>Forgot Password</h1>
        <form action="" method="post">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Submit</button>
        </form>
    </article>
</body>
</html>