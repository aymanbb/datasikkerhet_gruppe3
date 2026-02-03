<?php
$host = '127.0.0.1';
$dbname = "test_database";
$dbuser = "test_user";
$dbpass = "strong_password";
$sub_database = "test";
$table_name = "register";

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

$pincode_valid = false;


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check get matching subjects to pin code. Should just be one.
    if(isset($_POST['request_view_subject'])){
        $pin_code = trim($_POST["subject_pincode"]);

        if (empty($pin_code)) {
            $message = "Boop.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "SELECT subject, subject_pin 
                    FROM register 
                    WHERE subject_pin = :subm_pin"
                );

                $stmt->execute(
                    [":subm_pin" => $pin_code]
                );

                $subject_match = $stmt->fetch();
                if($subject_match['subject_pin'] != null){
                    $pincode_valid = true;
                }

            }catch(PDOException $e) {
                die("Serious error message for serious problems" . $e->getMessage());
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
    <form method="post">
        <label for="pincode">Pincode:</label><br>
        <input
            type="text"
            id="pincode"
            name="subject_pincode"
            maxlength="4"
            pattern="\d{4}"
            required
        >
        <br><br>
        <button name="request_view_subject" type="submit">View subject page</button>
        <?php if ($pincode_valid == true): ?>
            <p class="validity">wait, it's real?</p>
        <?php else: ?>
                <p class="validity">Not real so far</p>
        <?php endif; ?>
    </form>
</body>
</html>