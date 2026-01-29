<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    exit('Please complete the registration form!');
}

if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    exit('Please complete the registration form');
}

if ($stmt = $con->prepare('SELECT id, password FROM users WHERE username = ?' )) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'Username already exists! Please choose another!';
    } else {
	// Declare variables
        $registered = date('Y-m-d H:i:s');
	// Hash passwords
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	// Username does not exist, insert new account
	if ($stmt = $con->prepare('INSERT INTO users (username, password, email, registered) VALUES (?, ?, ?, ?)')) {
	    // Bind POST data to the prepared statement
	    $stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $registered);
	    $stmt->execute();

	    header('Location: login.php');
	} else {
	    echo 'Could not prepare statement!';
	}
    }

    $stmt->close();
} else {
    echo 'Could not prepare statement!';
}

$con->close();
?>
