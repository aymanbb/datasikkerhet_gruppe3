<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();
$subjects = $db->subjectsFetchAll($subject_code);

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
    <a href="index.php">back to start =D</a>
    <section>
        <h1>Emner du har tilgang til</h1>
        <?php foreach ($subjects as $subject):
            $name = $subject['Subject_name'];
        ?>
            <article>
                <a href="#">
                    <h2><?= htmlspecialchars($name) ?></h2>
                </a>
            </article>
        <?php endforeach; ?>
    </section>
</body>

</html>