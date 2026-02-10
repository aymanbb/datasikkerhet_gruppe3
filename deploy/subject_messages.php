<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

// NOTE: det skal være mulig å hente "subject pin" fra $_GET['ref'] her, om man blir omdirigert fra guest_login.php 
// Burde det være en default verdi??
$subject_pin = "6666";
if(!validateSubjectPin($_GET['ref'])){
    $subject_pin = $_GET['ref'];
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(isset($_GET['test-melding-submit'])){
        $user_id = SessionGetUserID();
        $new_message = htmlspecialchars($_GET["test-melding"]);
        //answer
        //subject_ID
        $db->subjectMessageSubmit($user_id, (int)$subject_pin, $new_message);
    } 
    $subject_messages = $db->subjectMessageFetchAll($subject_pin);  
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
                        }
                    }
                }
            }
        </style>
    </head>
    <body>
        <a href="index.php">back to start =D</a>
        <a href="#send_message" id="skip">Jump to contribute</a>
        <section>
        <h1>$emnenavn</h1>
            <article>
                <h2>Foreleser</h2>
                <p>Foreleser for $emnenavn er $forelesernavn. Kan nås på e-post: $foreleserepost</p>
                <img src="" alt="Photo of the lecturer">
            </article>
        <?php foreach ($subject_messages as $subject_message): ?>
            <article>
                <h3>Fra anonym:</h3>
                <p><?= htmlspecialchars($subject_message['Message_ID']) ?></p>
                <p class="message"><?= htmlspecialchars($subject_message['Message_body']) ?></p>
            </article>
        <?php endforeach; ?>
        </section>
        <article>
            <h2>Delta i samtalen!</h2>
            <?php if ($message != ""): ?>
                <p class="melding"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            
            <form method="get">
                <label for="test-melding" id="send_message">Skriv din melding her</label>
                <textarea name="test-melding" maxlength="256" rows="10" cols="50" required></textarea>            
                <button type="submit" name="test-melding-submit">Send</button>
            </form>
        </article>
    </body>
</html>