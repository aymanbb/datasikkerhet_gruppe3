<?php
    $host = '127.0.0.1';
    $dbname = "g3_database_actual";
    $dbuser = "test_user";
    $dbpass = "strong_password";
    $users_table = "users";
    $subject_table = "subject";
    $messages_table = "messages";
    $comments_table = "comments";
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
                $pin = $_POST["register_pin"];
                $image = $_POST["register_image"];

                if (empty($username) || empty($email) || empty($password) || empty($subject) || empty($pin)) {
                    $message = "All fields are required.";
                } else {

                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO $users_table (Name_User, Email, Password, Is_teacher, Subject_name, Subject_PIN)
                            VALUES (:username, :email, :password, 1, :subject, :subject_pin)"
                            //ignoring "picture filename", "subject_ID", "subject pin" and "session_cookie"
                        );

                        $stmt->execute([
                            ":username" => $username,
                            ":email" => $email,
                            ":password" => $password,
                            ":subject" => $subject,
                            ":subject_pin" => $pin,
                        ]);

                        $message = "Registration successful!";
                    } catch (PDOException $e) {
                        echo $e->getMessage();
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

            h1{
                display: flex;
                justify-content: center;
            }

            nav ul {
                padding: 0;
                list-style: none;
                margin: auto;
                display: flex;
                justify-content: center;

                li {
                    margin: 0.5rem;

                    a{
                        text-decoration: none;
                    }
                }
            }
        
            article {
                    border: 3px solid black;
                    padding: 2rem;
                    width: 50dvw;
                    margin: 1rem auto 1rem auto;

                    form{
                            display: flex;
                            flex-direction: column;

                            label{
                                    padding-top: 1rem;
                            }

                            button{
                                margin-top: 1rem;
                            }
                    }
                }

            section {
                display: flex;
                flex-direction: column;
                padding: 2rem 0rem 2rem 0rem;
            }
        }
    </style>
</head>
<body>
    <h1>Registrer foreleser</h1>
    <nav>
        <ul>
            <li><a href="index.php">Gå til forsiden</a></li>
            <li><a href="guest_login.php">Fortsett som gjest</a></li>
            <li><a href="#">Glemt passord?</a></li>
            <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
            <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
        </ul>
    </nav>
    <article>
        <h2>Registrer deg som foreleser</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <form action="" method="post">
            <label for="register-username">Navn:</label>
            <input type="text" id="register-username" name="register_username" required>

            <label for="register-email">E-post:</label>
            <input type="email" id="register-email" name="register_email" required>

            <label for="register-password">Passord:</label>
            <input type="password" id="register-password" name="register_password" required>

            <label for="subject-name">Emnenavn:</label>
            <input type="text" id="subject-name" name="register_subject" required>

            <label for="subject-pin">PIN-kode for emne:</label>
            <input type="text" id="subject-pin" name="register_pin" pattern="[0-9]*" maxlength="4" required>

            <label for="foreleser-image">Last opp bilde av deg selv:</label>
            <input type="file" id="foreleser-image" name="register_image" accept="image/*" required>

            <button type="submit" name="foreleser_register_submit">Send inn</button>

            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

        </form>
    </article>
</body>
</html>
