<?php
    $host = '127.0.0.1';
    $dbname = "g3_database_actual";
    $dbuser = "test_user";
    $dbpass = "strong_password";
    $users_table = "users";
    $subject_table = "subject";

    /*KOPIERT FRA SUBJECT_MESSAGES AND THEREFORE DOES NOT WORK*/
    try {
        $stmt = $pdo->prepare(
            "select emne_id, message  
            from mock_database 
            where emne_id = :subject_code"
        );

        $stmt->execute(
            [":subject_code" => $subject_code]
        );
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            die("Serious error message for serious problems" . $e->getMessage());
        }
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
                $name = $subject['name'];
            ?>
                <article>
                    <a href="#"><h2><?= htmlspecialchars($name) ?></h2></a>
                </article>
            <?php endforeach; ?>
        </section>
    </body>
</html>