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


$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(isset($_GET['test-melding-submit'])){
        $melding = htmlspecialchars($_GET["test-melding"]);
    }
}
  
// Fetch all users
try {
    $stmt = $pdo->query(
        "SELECT emne_id, message
         FROM mock_database
         ORDER BY emne_id ASC"

    );
    $subject_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $subject_messages = [];
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
         <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$Emne-meldinger c:</title>
        <style>
            body article form {
                display: flex;
                flex-direction: column;
                textarea {
                    /*width: 600px;
                    height: 300px;*/
                    resize: none;
                    border: 1px solid black;
                    border-radius: 5%;
                    max-width: 40dvw;

                }
            }
        </style>
    </head>
    <body>
        <a href="index.php">back to start =D</a>
        <h1>
            Meldinger for emnet: $variabelnavn her
        </h1>

        <article>
            <h3>
                Melding fra: $Admin
            </h3>
            <p>
                Velkommen til forumet $emnenavn
            </p>
            <p>
                <!-- fetched contents'
                keystring = emneID

                $key = $_GET['keystring'];
                $results = mysql_query("SELECT * FROM users WHERE keystring='".mysql_real_escape_string($key)."'");    
                while ($row = mysql_fetch_array($results)) {
                }
                  -->
            </p>
            <?php if ($melding != ""): ?>
                <p class="melding"><?= htmlspecialchars($melding) ?></p>
            <?php endif; ?>
            
            <form method="get">
                <label for="test-melding">skriv noe her</label>
                <textarea name="test-melding" maxlength="256" rows="10" cols="50" required></textarea>            
                <button type="submit" name="test-melding-submit">Register</button>
            </form>
        </article>
        <article>
            <form>
                <label for="content">Skriv din melding her</label>
                <textarea name="content" maxlength="256" rows="10" cols="50" required></textarea>
            </form>
        </article>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>

    </body>

</html>