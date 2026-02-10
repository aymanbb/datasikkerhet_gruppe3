<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
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
            <?php foreach ($subjects as $subject): 
                $name = $subject['Subject_name'];
            ?>
                <article>
                    <a href="#"><h2><?= htmlspecialchars($name) ?></h2></a>
                </article>
            <?php endforeach; ?>
        </section>
    </body>
</html>