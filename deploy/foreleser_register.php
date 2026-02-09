<?php

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/session.php';

    $db = new Database();

    $message = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if(isset($_POST['foreleser_register_submit'])){
            $username = trim($_POST["register_username"]);
            $email = trim(string: $_POST["register_email"]);
            $password = $_POST["register_password"];
            $subject = trim($_POST["register_subject"]);
            $pin = $_POST["register_pin"];
            $image = $_POST["register_image"];

            if($db->userLecturerRegister($username, $email, $password, $subject, $subject_code, $pin, $image)){
                //FIXME: some error message here
            }else{
                // FIXME: Some error message here
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foreleser Registration</title>
    <style>
        body {
            margin: 3rem;
        }
        article {
            padding: 0rem 3rem 0rem 3rem;
        }
        form {
            display: flex;
            flex-direction: column;
            max-width: 33dvw;
        }
        label {
            padding-top: 1rem;
        }
        section {
            display: flex;
            flex-direction: column;
            padding: 2rem 0rem 2rem 0rem;
        }
    </style>
</head>
<body>
    <a href="index.php">back to start =D</a>
    <h1>Foreleser Registration Page</h1>
    <article>
        <h2>Register as a Foreleser</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <form action="" method="post">
            <label for="register-username">Username:</label>
            <input type="text" id="register-username" name="register_username" required>

            <label for="register-email">Email:</label>
            <input type="email" id="register-email" name="register_email" required>

            <label for="register-password">Password:</label>
            <input type="password" id="register-password" name="register_password" required>

            <label for="subject-name">Name of Subject:</label>
            <input type="text" id="subject-name" name="register_subject" required>

            <label for="subject-pin">PIN for Subject:</label>
            <input type="text" id="subject-pin" name="register_pin" pattern="[0-9]*" maxlength="4" required>

            <label for="foreleser-image">Upload Image of Yourself:</label>
            <input type="file" id="foreleser-image" name="register_image" accept="image/*" required>

            <button type="submit" name="foreleser_register_submit">Register</button>

            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

        </form>
    </article>

    <section>
        <a href="guest.php">Continue as Guest</a>
        <a href="#">Forgotten password?</a>
    </section>
</body>
</html>
