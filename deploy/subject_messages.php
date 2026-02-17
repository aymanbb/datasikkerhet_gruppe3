<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

// NOTE: det skal være mulig å hente "subject pin" fra $_GET['ref'] her, om man blir omdirigert fra guest_login.php 
// Burde det være en default verdi??
$subject_pin = 6666;
// testing in progress, do not remove comments
//if(validateSubjectPin($_GET['ref'])){
//    $subject_pin = $_GET['ref'];
//}

$user_id = $_SESSION['user']['id'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(isset($_GET['test-melding-submit'])){
        $user_id = $_SESSION['user']['id'];

        if (isset($_GET['test-melding'])) {
            $new_message = trim((string)$_GET['test-melding']);
        }

        //answer
        //subject_ID
        $db->subjectMessageSubmit((int)$user_id, (int)$subject_pin, $new_message);
    }
    $subject_messages = $db->subjectMessageFetchAll((int)$subject_pin);
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

                nav{
                    margin: 0.5rem;

                    a{
                        text-decoration: none;
                    }
                } 

                button {
                    max-width: max-content;
                }

                section {
                    display: flex;
                    flex-direction: column;

                    h1 {
                        justify-content: center;
                        display: flex;
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
                        

                        .melding{
                            border: 1px solid black;
                            padding: 4px;
                        }
                    }
                }
            
                article {
                    display: flex;
                    flex-direction: column;
                    border: 3px solid black;
                    padding: 2rem;
                    width: 50dvw;
                    margin: 1rem auto 1rem auto;

                    h2{
                        width: 50dvw;
                    }

                    img{
                        max-width: 150px;
                    }

                    form {
                        display: flex;
                        flex-direction: column;
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
        <a href="#send_message" id="skip">Hopp til bunnen</a>
        <section>
            <h1>$emnenavn</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Gå til forsiden</a></li>
                    <li><a href="guest_login.php">Fortsett som gjest</a></li>
                    <li><a href="#">Glemt passord?</a></li>
                    <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
                    <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
                </ul>
            </nav>
            <article>
                <h2>Foreleser</h2>
                <p>Foreleser for $emnenavn er $forelesernavn. Kan nås på e-post: $foreleserepost</p>
                <img src="" alt="Bilde av foreleser">
            </article>
            <?php foreach ($subject_messages as $subject_message): ?>
                <article>
                    <h3><?= 'Message_ID: ' . htmlspecialchars($subject_message['Message_ID']) . " " ?>Fra anonym:</h3>
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
                <input type="hidden" name="ref" value="<?php echo $subject_pin; ?>">              
            </form>
        </article>
    </body>
</html>