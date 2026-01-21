<?php
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
            <input type="text" id="subject-name" name="subject_name" required>

            <label for="subject-pin">PIN for Subject:</label>
            <input type="text" id="subject-pin" name="subject_pin" pattern="[0-9]*" maxlength="4" required>

            <label for="foreleser-image">Upload Image of Yourself:</label>
            <input type="file" id="foreleser-image" name="foreleser_image" accept="image/*" required>

            <button type="submit" name="foreleser_register_submit">Register</button>
        </form>
    </article>

    <section>
        <a href="guest.php">Continue as Guest</a>
        <a href="#">Forgotten password?</a>
    </section>
</body>
</html>
