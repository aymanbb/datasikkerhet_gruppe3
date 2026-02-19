<?php
require_once __DIR__ . '/includes/session.php';

// Start or resume session (harmless if sessions are auto-started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear session variables
$_SESSION = [];
session_unset();

// Delete the session cookie (if sessions use cookies)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy server-side session data
session_destroy();

// Optional: remove a persistent "remember me" cookie if you use one
// setcookie('remember_me', '', time() - 42000, '/');

// Redirect to homepage (or login page)
header('Location: index.php');
exit;
?>