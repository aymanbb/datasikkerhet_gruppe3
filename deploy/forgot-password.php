<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['email'])) {
        $email = trim($_POST['email']);

        // Validate email
        if (empty($email)) {
            echo "Please enter your email address.";
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $stmt = $pdo->prepare("SELECT email FROM t_users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $email_addr = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($email_addr) {
                    echo "Here's your new password: F4xeJlZkmn33";
                } else {
                    echo "Email address not found.";
                }
            } catch (PDOException $e) {
                echo "Server error. Please try again.";
            }
        } else {
            echo "Please enter a valid email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional: Link to your CSS file -->
</head>
<body>
    <h1>Forgot Password</h1>
    <form action="" method="post">
        <label for="email">Enter your email address:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
