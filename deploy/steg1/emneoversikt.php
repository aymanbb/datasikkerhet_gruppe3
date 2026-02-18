<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';




$db = new Database();

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

$user_id = getSessionUserId();
$user = $db->userFindById($user_id);
$is_guest = isset($_SESSION['guest']);

// FLAGS
$is_teacher = false;
if ($user != null) {
    $is_teacher = (bool)$user['is_teacher'];
}

if ($is_teacher) {
    $subject = $db->findSubjectByLecturerId($user['user_id']);
    try {
        $params = http_build_query([
            'ref' => $subject['subject_id']
        ]);
        header("Location: subject_messages.php?" . $params, true, 303);
        exit;
    } catch (PDOException $e) {
        die("Serious error message for serious problems" . $e->getMessage());
    }
}

$subjects = $db->subjectsFetchAll();
//$username = "tomh";
//$user = $db->userFindByUsername($username);
//$subject = $db->findSubjectByLecturerId($_SESSION['user']['id']);

//echo $user['is_teacher'];
//echo $user['username'];
//echo $subject['subject_id'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Emneoversikt</title>
        <style>
            section {
                    display: flex;
                    flex-direction: column;

                    h1 {
                        margin: auto;
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
                    }
                }
        </style>
    </head>
    <body>
        <section>
            <h1>Emner du har tilgang til</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Gå til forsiden</a></li>
                    <li><a href="guest_login.php">Fortsett som gjest</a></li>
                    <li><a href="#">Glemt passord?</a></li>
                    <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
                    <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
                </ul>
            </nav>
            <?php
                foreach ($subjects as $subject):
                    $name = $subject['subject_name'];
                ?>
                <article>
                    <a href="<?="subject_messages.php?ref=". $subject['subject_id'] ?>"><h2><?= htmlspecialchars($name) ?></h2></a>
                </article>
            <?php endforeach; ?>
        </section>
    </body>
</html>