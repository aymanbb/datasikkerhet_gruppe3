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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check get matching subjects to pin code. Should just be one.
    if(isset($_POST['request_view_subject'])){
        $submitted_subject_pin = trim($_POST["subject_pincode"]);

        if (empty($submitted_subject_pin)) {
            $message = "Boop.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "SELECT subject, subject_pin 
                    FROM register 
                    WHERE subject_pin = :subm_pin"
                );

                $stmt->execute(
                    [":subm_pin" => $submitted_subject_pin]
                );

                $subject_match = $stmt->fetch();
                if($subject_match['subject_pin'] != null){
                    $params = http_build_query([
                        'ref' => $submitted_subject_pin
                    ]);
                    header("Location: subject_messages.php?ref=" . $params, true, 303);
                    exit;
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
    <a href="index.php">Home</a>
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
    </form>
</body>
</html>