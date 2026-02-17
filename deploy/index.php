<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/login.php';

$db = new Database();
$login = new Login();
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['login_submit'])) { // Login for users and lecturers
        $username = trim($_POST["login_username"]);
        $password = $_POST["login_password"];

        if (empty($username) || empty($password)) {
            $message = "All fields are required.";
        } else {
            $login->login($username, $password);
        }
    } 
    elseif (isset($_POST['register_submit']))  { // Register normal student user
        $username = trim($_POST["register_username"]);
        $email = trim($_POST["register_email"]);
        $password = ($_POST["register_password"]);
        $db->userStudentRegister($username, $subject_code, $password);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gruppe 3</title>
    <style>
        body {
                margin: 3rem;

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

                h1{
                    display: flex;
                    justify-content: center;
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
    <h1>Velkommen til gruppe 3 sin supersikre hjemmeside!</h1>
    <nav>
        <ul>
            <li><a href="guest_login.php">Fortsett som gjest</a></li>
            <li><a href="#">Glemt passord?</a></li>
            <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
            <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
        </ul>
    </nav>
    <article>
        <h2>Logg inn</h2>
        <form action="" method="post">
            <label for="login-username">Navn:</label>
            <input type="text" id="login-username" name="login_username" required>

            <label for="login-password">Passord:</label>
            <input type="password" id="login-password" name="login_password" required>

            <button type="submit" name="login_submit">Logg inn</button>
        </form>
    </article>

    <article>
        <h2>Registrer deg</h2>
        <a href="foreleser_register.php">Registrering for forelesere</a>
        <form action="" method="post">
            <label for="register-username">Navn:</label>
            <input type="text" id="register-username" name="register_username" required>

            <label for="register-email">E-post:</label>
            <input type="email" id="register-email" name="register_email" required>

            <label for="register-password">Passord:</label>
            <input type="password" id="register-password" name="register_password" required>

            <button type="submit" name="register_submit">Send inn</button>
        </form>
    </article>
</body>

</html>