<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$subject_id = $_GET['ref'];

// sjekker om bruker er logget inn, eller er gjest med tilgang til emne
//if ($_SESSION['guest'] == true && $_SESSION['subject_permitted'] == $subject_id || isset($_SESSION['logged_in'])) {
if (!isset($_SESSION['logged_in']) && ($S_SESSION['guest'] != true && $_SESSION['permitted_subject'] != $subject_id)) {
    header('Location: index.php');
}

$db = new Database();

// validering paa vei?

$emne_info = $db->getSubjectInfo($subject_id);
$emnenavn = $emne_info['subject_name'];
$foreleser = $db->userFindById($emne_info['teacher_id']);
$foreleser_img = "/steg1//media/" . $foreleser['picture_filename'];

$user_id = $_SESSION['user']['id'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(isset($_GET['test-melding-submit'])){
        $user_id = $_SESSION['user']['id'];

        if (isset($_GET['test-melding'])) {
            $new_message = trim((string)$_GET['test-melding']);
        }

        //answer
        //subject_ID
        $db->subjectMessageSubmit((int)$user_id, (int)$subject_id, $new_message);
    }
    $subject_messages = $db->subjectMessageFetchAll((int)$subject_id);
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($emnenavn ?? '', ENT_QUOTES, 'UTF-8') ?> meldinger</title>
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
            <h1><?= htmlspecialchars($emnenavn ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Gå til forsiden</a></li>
                    <li><a href="guest_login.php">Fortsett som gjest</a></li>
                    <li><a href="forgot-password.php">Glemt passord?</a></li>
                    <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
                    
                </ul>
            </nav>
            <article>
                <h2>Foreleser</h2>
                <p>Foreleser for <?= htmlspecialchars($emnenavn ?? '', ENT_QUOTES, 'UTF-8') ?> er <?= htmlspecialchars($foreleser['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>. Kan nås på e-post: <?= htmlspecialchars($foreleser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <img src="<?php echo htmlspecialchars($foreleser_img); ?>" alt="Bilde av foreleser">
            </article>
            <?php foreach ($subject_messages as $subject_message): ?>
                <article>
                    <h3><?= 'Melding nr. ' . htmlspecialchars($subject_message['message_id']) . " " ?>Fra anonym:</h3>
                    <p class="message"><?= htmlspecialchars($subject_message['message_body']) ?></p>
                        <?php if($subject_message['answer']): ?>
                                <p class="answer"> <?= htmlspecialchars($subject_message['answer']) ?> </p>
                        <?php else: ?>
                                <form action="" method="POST">
                                        <input type="hidden" name="message_id" value="<?= $subject_message['message_id'] ?>">
                                        <textarea name="answer" maxlength="256" rows="10" cols="50"></textarea>
                                        <button type="submit" name="answer_submit">Svar</button>
                                </form>
                        <?php endif; ?> 
                </article
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
                <input type="hidden" name="ref" value="<?php echo $subject_id; ?>">
            </form>
        </article>
    </body>
</html>