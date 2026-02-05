<?php
    $host = '127.0.0.1';
    $dbname = "g3_database_actual";
    $dbuser = "test_user";
    $dbpass = "strong_password";
    $users_table = "users";
    $subject_table = "subject";
    $messages_table = "messages";
    $comments_table = "comments";
    /*$subject_code = $_GET['subject_code'] ?? null; Denne må definere ALLE fagene brukeren har tilgang til*/
    $subject_code = 1234;

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

    /*KOPIERT FRA SUBJECT_MESSAGES AND THEREFORE DOES NOT WORK*/
    try {
        $stmt = $pdo->prepare(
            "select Subject_ID, Subject_name  
            from $subject_table 
            where Subject_PIN = :subject_code"
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
                $name = $subject['Subject_name'];
            ?>
                <article>
                    <a href="#"><h2><?= htmlspecialchars($name) ?></h2></a>
                </article>
            <?php endforeach; ?>
        </section>
    </body>
</html>