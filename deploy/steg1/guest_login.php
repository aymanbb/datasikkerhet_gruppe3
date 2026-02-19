<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

if (isset($_SESSION['logged_in'])) {
    header("Location: emneoversikt.php");
    exit;
}

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check get matching subjects to pin code. Should just be one.
    if (isset($_POST['request_view_subject'])) {
        $submitted_subject_pin = (int)trim($_POST["subject_pincode"]);

        $subject = $db->subjectPinExists($submitted_subject_pin);

        if (!$subject) {
            echo "Please, for the love of god!";
        }
        else {
            try {
                $_SESSION['guest'] = true;
                $_SESSION['permitted_subject'] = $subject['subject_id'];
                $_SESSION['can_message'] = false;
                $_SESSION['can_answer'] = true;
                $params = http_build_query([
                    'ref' => $_SESSION['permitted_subject']
                ]);
                header("Location: subject_messages.php?" . $params, true, 303);
                exit;
            } catch (PDOException $e) {
               die("Serious error message for serious problems" . $e->getMessage());
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Subject pincode Login</title>
        <style>
            body{
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

                form {
                    border: 3px solid black;
                    padding: 2rem;
                    width: 50dvw;
                    margin: 1rem auto 1rem auto;
                }
            }
        </style>
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="guest_login.php">Fortsett som gjest</a></li>
                <li><a href="forgot-password.php">Glemt passord?</a></li>
            </ul>
        </nav>
        <h1>Logg inn som gjest</h1>
        <form method="post">
            <label for="pincode">Emne-PIN:</label><br>
            <input
                type="text"
                id="pincode"
                name="subject_pincode"
                maxlength="4"
                pattern="\d{4}"
                required
            >
            <br><br>
            <button name="request_view_subject" type="submit">Se emneside</button>
        </form>
    </body>
</html>