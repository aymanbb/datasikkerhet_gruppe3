<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

$user = $db->userFindById($_SESSION['user']['id']);

if ($user['is_teacher'] == 1) {
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
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <section>
            <h1>Emner du har tilgang til</h1>
            <nav>
                <ul>
                    <li><a href="dokumentasjon.html">Dokumentasjon</a></li>
                    <li><a href="logout.php">Logg ut</a></li>
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