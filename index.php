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

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if(isset($_POST['login_submit'])){
            $username = trim($_POST["login_username"]);
            $password = $_POST["login_password"];

            if (empty($username) || empty($password)) {
                $message = "All fields are required.";
            } else {

                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO test (username, password)
                        VALUES (:username, :password)"
                    );

                    $stmt->execute([
                        ":username" => $username,
                        ":password" => $password
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
        }elseif(isset($_POST['register_submit'])){


            $username = trim($_POST["register_username"]);
            $email = trim(string: $_POST["register_email"]);
            $password = $_POST["register_password"];

            if (empty($username) || empty($email) || empty($password)) {
                $message = "All fields are required.";
            } else {

                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO register (username, email, password)
                        VALUES (:username, :email, :password)"
                    );

                    $stmt->execute([
                        ":username" => $username,
                        ":email" => $email,
                        ":password" => $password
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
    <title>Simple Authentication Page</title>
</head>
<body>
    <h1>Velkommen til gruppe 3 sin supersikre hjemmeside!</h1>

    <section>
        <h2>Login</h2>
        <form action="" method="post">
            <label for="login-username">Username:</label>
            <input type="text" id="login-username" name="login_username" required>

            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="login_password" required>

            <button type="submit" name="login_submit">Login</button>
        </form>
    </section>

    <section>
         <h2>Register</h2>
        <a href="#">Registrering for forelesere</a>
        <form action="" method="post">
            <label for="register-username">Username:</label>
            <input type="text" id="register-username" name="register_username" required>

            <label for="register-email">Email:</label>
            <input type="email" id="register-email" name="register_email" required>

            <label for="register-password">Password:</label>
            <input type="password" id="register-password" name="register_password" required>

            <button type="submit" name="register_submit">Register</button>
        </form>
    </section>

    <div>
        <a href="guest.php">Continue as Guest</a>
        <a href="passrestore.php">Forgotten your password?</a>
    </div>
</body>
</html>