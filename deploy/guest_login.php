<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check get matching subjects to pin code. Should just be one.
    if (isset($_POST['request_view_subject'])) {
        $submitted_subject_pin = (int)trim($_POST["subject_pincode"]);

        if (!$db->subjectPinExists($submitted_subject_pin)) {
            echo "Please, for the love of god!";
        } else {
            try {
                $params = http_build_query([
                    'ref' => $submitted_subject_pin
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
        <h1>Logg inn som gjest</h1>
        <nav>
            <ul>
                <li><a href="index.php">Gå til forsiden</a></li>
                <li><a href="guest_login.php">Fortsett som gjest</a></li>
                <li><a href="#">Glemt passord?</a></li>
                <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
                <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
            </ul>
        </nav>
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