<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

$subject_id = isset($_REQUEST['ref']) ? (int)$_REQUEST['ref'] : 0;

// sjekker om bruker er logget inn, eller er gjest med tilgang til emne
//if ($_SESSION['guest'] == true && $_SESSION['subject_permitted'] == $subject_id || isset($_SESSION['logged_in'])) {
if (!isset($_SESSION['logged_in']) && (!isset($_SESSION['guest']) || !isset($_SESSION['permitted_subject']))) {
    header('Location: index.php');
    exit;
}

// validering paa vei?
$emne_info = $db->getSubjectInfo((int)$subject_id);
$emnenavn = $emne_info['subject_name'];
$foreleser = $db->userFindById((int)$emne_info['teacher_id']);
$foreleser_img = "/steg1/media/" . $foreleser['picture_filename'];

$user_id = $_SESSION['user']['id'] ?? null;
$user = null;
if (!empty($user_id)) {
    $user = $db->userFindById((int)$user_id);
}

// Normalize session flags to booleans
$user_can_message = !empty($_SESSION['can_message']);
$user_can_answer  = !empty($_SESSION['can_answer']);

$message = '';

$subject_messages = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['answer_submit'])) {
        // Only allow answering if session flag permits it
        if (! $user_can_answer) {
            $message = 'Du har ikke tillatelse til å kommentere/svare.';
        } else {
            $msgId = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
            $answerText = isset($_POST['answer']) ? trim((string)$_POST['answer']) : '';
            if ($msgId > 0 && $answerText !== '') {
                // If logged in user is a teacher, use subjectMessageAnswerSubmit
                if (!empty($user) && !empty($user['is_teacher'])) {
                    $db->subjectMessageAnswerSubmit($msgId, $answerText);
                }
                // Else if this is a guest session, use messageCommentSubmit
                elseif (!empty($_SESSION['guest'])) {
                    $db->messageCommentSubmit($msgId, $answerText);
                }
                // Otherwise, no valid submitter found
                else {
                    $message = 'Ugyldig bruker for innsending av svar.';
                    // fetch messages and skip redirect
                    $subject_messages = $db->subjectMessageFetchAll((int)$subject_id);
                    // stop further processing
                    goto render_page;
                }

                // redirect to avoid duplicate submission on refresh
                header("Location: " . $_SERVER['PHP_SELF'] . "?ref=" . $subject_id);
                exit;
            } else {
                $message = 'Ugyldig svar.';
            }
        }
    }
    $subject_messages = $db->subjectMessageFetchAll((int)$subject_id);
}

// Handle GET (message submissions and initial load)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // message submission using GET (kept as in your HTML)
    if (isset($_GET['test-melding-submit'])) {
        // Only allow sending a message if session flag allows it
        if (! $user_can_message) {
            $message = 'Du har ikke tillatelse til å sende melding for dette emnet.';
        } else {
            $user_id = $user_id ?? ($_SESSION['user']['id'] ?? null);
            $new_message = '';
            if (isset($_GET['test-melding'])) {
                $new_message = trim((string)$_GET['test-melding']);
            }
            if (!empty($user_id) && $new_message !== '') {
                $db->subjectMessageSubmit((int)$user_id, (int)$subject_id, $new_message);
                // redirect to clean the query (prevents resubmits)
                header("Location: " . $_SERVER['PHP_SELF'] . "?ref=" . $subject_id);
                exit;
            } else {
                $message = 'Ugyldig melding eller ikke logget inn.';
            }
        }
    }
    // fetch messages for display
    $subject_messages = $db->subjectMessageFetchAll((int)$subject_id);
}

render_page:

function answer_present($val) {
    return ($val !== null && $val !== '' && $val !== 'NULL');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($emnenavn ?? '', ENT_QUOTES, 'UTF-8') ?> meldinger</title>
        <link rel="stylesheet" href="styles/style_subjectmessages.css">
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
                <p>
                    Foreleser for <?= htmlspecialchars($emnenavn ?? '', ENT_QUOTES, 'UTF-8') ?>
                    er <?= htmlspecialchars($foreleser['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>.
                    Kan nås på e-post:
                    <?= htmlspecialchars($foreleser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <img src="<?= htmlspecialchars($foreleser_img ?? '/steg1/media/default.png', ENT_QUOTES, 'UTF-8') ?>"
                     alt="Bilde av foreleser">
            </article>

            <?php foreach ($subject_messages as $subject_message): ?>
                <article>
                    <h3>
                        Fra anonym:
                    </h3>

                    <p class="message"><?= htmlspecialchars($subject_message['message_body'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                    <?php if (answer_present($subject_message['answer'] ?? null)): ?>
                        <section>
                            <h4>Svar fra foreleser:</h4>
                            <p class="comment-answer"><?= htmlspecialchars($subject_message['answer'], ENT_QUOTES, 'UTF-8') ?></p>
                        </section>
                    <?php endif; ?>

                    <?php
                    // fetch comments for this message (returns array or empty array)
                    $subject_comments = $db->messageCommentsFetchAll((int)$subject_message['message_id']);
                    if (!empty($subject_comments) && is_array($subject_comments)):
                        foreach ($subject_comments as $comment): ?>
                        <section>
                            <h4>Anonym kommentar:</h4>
                            <p class="comment-answer"><?= htmlspecialchars($comment['comment_body'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        </section>
                        <?php endforeach;
                    endif;
                    ?>

                    <?php if ($user_can_answer): ?>
                        <form action="" method="POST">
                            <input type="hidden" name="message_id" value="<?= htmlspecialchars($subject_message['message_id'], ENT_QUOTES, 'UTF-8') ?>">
                            <textarea name="answer" maxlength="256" rows="5" cols="50"></textarea>
                            <button type="submit" name="answer_submit">Svar</button>
                        </form>
                    <?php endif; ?>
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
                <input type="hidden" name="ref" value="<?php echo $subject_id; ?>">
            </form>
        </article>
    </body>
</html>