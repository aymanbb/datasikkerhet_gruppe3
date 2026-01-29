<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Start the session
session_start();

if (!isset($_POST['username'], $_POST['password'])) {
     exit('please fill both the username and password fields!');
}
if ($stmt = $con->prepare('SELECT id, password, role FROM users WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

// Check if account exists with the input username
if ($stmt->num_rows > 0) {
    // Account exists, so bind the results to variables
    $stmt->bind_result($id, $password, $role);
    $stmt->fetch();
    // Note: remember to use password_hash in your registration file to store the hashed passwords
    if (password_verify($_POST['password'], $password)) {
        // Password is correct! User has logged in!
        // Regenerate the session ID to prevent session fixation attacks
        session_regenerate_id();
        // Declare session variables (they basically act like cookies but the data is remembered on the server)
        $_SESSION['account_loggedin'] = TRUE;
        $_SESSION['account_name'] = $_POST['username'];
        $_SESSION['account_id'] = $id;
        
	if ($role === "student") {
            header('Location: home.php');
	} else if ($role === "foreleser") {
	    header('Location: meldinger.php');
	}
        exit;
    } else {
        // Incorrect password
        echo 'Incorrect username and/or password!';
    }
} else {
    // Incorrect username
    echo 'Incorrect username and/or password!';
}

    $stmt->close();
}
?>
