<?php
    $host = '127.0.0.1';
    $dbname = "test_database";
    $dbuser = "test_user";
    $dbpass = "strong_password";
    $sub_database = "test";
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $dbuser,
            $dbpass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    $message = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            if(isset($_POST['foreleser_register_submit'])){

                $username = trim($_POST["register_username"]);
                $email = trim(string: $_POST["register_email"]);
                $password = $_POST["register_password"];
                $subject = trim($_POST["register_subject"]);
                $subject_code = trim($_POST["register_subject_code"]);
                $pin = $_POST["register_pin"];
                $image = $_POST["register_image"];

                if (empty($username) || empty($email) || empty($password) || empty($subject) || empty($subject_code) || empty($pin) || empty($image)) {
                    $message = "All fields are required.";
                } else {

                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO register (username, email, password, subject, subject_code, pin)
                            VALUES (:username, :email, :password, :subject, :subject_code, :pin)"
                        );

                        $stmt->execute([
                            ":username" => $username,
                            ":email" => $email,
                            ":password" => $password,
                            ":subject" => $subject,
                            ":subject_code" => $subject_code,
                            ":pin" => $pin,
                        ]);

                        $message = "Registration successful!";
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            $message = "Username or email already exists.";
                        } else {
                            $message = "An error occurred.";
                        }
                    }
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

            <label for="subject-code">Subject code:</label>
            <input type="text" id="subject-code" name="register_subject_code" maxlength="8" minlength="8" pattern="[A-Za-z]{3}[0-9]{5}" required>

            <label for="subject-pin">PIN for Subject:</label>
            <input type="text" id="subject-pin" name="register_pin" pattern="[0-9]*" maxlength="4" required>

            <label for="foreleser-image">Upload Image of Yourself:</label>
            <input type="file" id="foreleser-image" name="register_image" accept="image/*" required>

            <button type="submit" name="foreleser_register_submit">Register</button>
        </form>
    </article>

    <section>
        <a href="guest.php">Continue as Guest</a>
        <a href="#">Forgotten password?</a>
    </section>
</body>
</html>
