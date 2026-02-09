<?php

    $host = '127.0.0.1';
    $dbname = "g3_database_actual";
    $dbuser = "test_user";
    $dbpass = "strong_password";
    $users_table = "users";
    $messages_table = "messages";
    $comments_table = "comments";
    $user_id = $_SESSION['user'];

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
// NOTE: det skal være mulig å hente "subject pin" fra $_GET['ref'] her, om man blir omdirigert fra guest_login.php 
$subject_code = "itf1000";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(isset($_GET['test-melding-submit'])){
        $user = $user_id;
        $new_message = htmlspecialchars($_GET["test-melding"]);
        //answer
        //subject_ID

        try{
            $stmt = $pdo->prepare(
                "INSERT INTO $messages_table(User_ID, Message_body, Answer, Subject_ID) 
                VALUES (:user, :subject_code, :new_message)"
            );

            $stmt->execute([
                        ":subject_code" => $subject_code,
                        ":new_message" => $new_message
                    ]);

            $message = "Registration successful!";

        } catch(PDOException $e){
            die("Oopsie woopsie! UWU we made a fucky wucky!!\n" . $e->getMessage());
        }

    }

    try {
        $stmt = $pdo->prepare(
            "select Subject_ID, Message_body 
            from $messages_table 
            where Subject_ID = :subject_code"
        );

        $stmt->execute(
            [":subject_code" => $subject_code]
        );

        $subject_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }catch(PDOException $e) {
        die("Serious error message for serious problems" . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$Emne-meldinger c:</title>
        <style>
            body {

                #skip {
                    bottom: 0;
                    position: fixed;
                    right: 3rem;
                    margin: 1rem;
                    padding: 1rem;
                    height: 3rem;
                    width: fit-content;
                    align-items: center;
                    display: flex;
                    font-size: 16px;
                    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
                    text-decoration: none;
                    border: 3px solid black;
                }

                button {
                    max-width: max-content;
                }

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
                        

                        .melding{
                            border: 1px solid black;
                            padding: 4px;
                        }
                    }
                }
            
                article {
                    display: flex;
                    flex-direction: column;

                    h2{
                        width: 50dvw;
                        margin: 3rem auto auto auto;
                    }

                    img{
                        max-width: 150px;
                    }

                    form {
                        display: flex;
                        flex-direction: column;
                        border: 3px solid black;
                        padding: 2rem;
                        width: 50dvw;
                        margin: 1rem auto 1rem auto;

                        textarea {
                            resize: none;
                        }

                        button {
                            padding: 3px 10px 3px 10px;
                            margin-top: 0.5rem;
                        }
                    }
                }
            }
        </style>
    </head>
    <body>
        <a href="index.php">back to start =D</a>
        <a href="#send_message" id="skip">Hopp til bunnen</a>
        <section>
        <h1>$emnenavn</h1>
            <article>
                <h2>Foreleser</h2>
                <p>Foreleser for $emnenavn er $forelesernavn. Kan nås på e-post: $foreleserepost</p>
                <img src="" alt="Bilde av foreleser">
            </article>
        <?php foreach ($subject_messages as $subject_message): ?>
            <article>
                <h3>Fra anonym:</h3>
                <p><?= htmlspecialchars($subject_message['emne_id']) ?></p>
                <p class="message"><?= htmlspecialchars($subject_message['message']) ?></p>
            </article>
        <?php endforeach; ?>
        </section>
        <article>
            <h2>Delta i samtalen!</h2>
            <?php if ($melding != ""): ?>
                <p class="melding"><?= htmlspecialchars($melding) ?></p>
            <?php endif; ?>
            
            <form method="get">
                <label for="test-melding" id="send_message">Skriv din melding her</label>
                <textarea name="test-melding" maxlength="256" rows="10" cols="50" required></textarea>            
                <button type="submit" name="test-melding-submit">Send</button>
            </form>
        </article>
    </body>
</html>