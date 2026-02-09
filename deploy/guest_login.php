<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check get matching subjects to pin code. Should just be one.
    if (isset($_POST['request_view_subject'])) {
        $submitted_subject_pin = trim($_POST["subject_pincode"]);

        if (!$db->subjectPinExists($submitted_subject_pin)) {
            // TODO: Some response here?
            $message = "Boop.";
        } else {
            if ($subject_match['subject_pin'] != null) {
                try {
                    $params = http_build_query([
                        'ref' => $submitted_subject_pin
                    ]);
                    header("Location: subject_messages.php?ref=" . $params, true, 303);
                    exit;
                } catch (PDOException $e) {
                    die("Serious error message for serious problems" . $e->getMessage());
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Subject pincode Login</title>
</head>

<body>
    <a href="index.php">Home</a>
    <form method="post">
        <label for="pincode">Pincode:</label><br>
        <input
            type="text"
            id="pincode"
            name="subject_pincode"
            maxlength="4"
            pattern="\d{4}"
            required>
        <br><br>
        <button name="request_view_subject" type="submit">View subject page</button>
    </form>
</body>

</html>