<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_name('session');
    session_start();
}

// Initialize rate limiter variables
$maxAttempts = 5; // Maximum attempts
$lockoutTime = 900; // Lockout period in seconds (15 minutes)

// Record of last activity to handle session expiration
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: index.php'); 
    }
// Function to reset login attempts
function resetAttempts() {
    global $lockoutTime;

    if (isset($_SESSION['last_attempt_time'])) {
        $elapsedTime = time() - $_SESSION['last_attempt_time'];
        if ($elapsedTime >= $lockoutTime) {
            $_SESSION['login_attempts'] = 0;
        }
    } else {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['last_attempt_time'] = time();
}

// Function to check lockout
function isLockedOut() {
    global $maxAttempts, $lockoutTime;

    if (isset($_SESSION['login_attempts']) && isset($_SESSION['last_attempt_time'])) {
        $elapsedTime = time() - $_SESSION['last_attempt_time'];
        return ($_SESSION['login_attempts'] >= $maxAttempts && $elapsedTime < $lockoutTime);
    }
    return false;
}

// Reset attempts if necessary
resetAttempts();

// Store the current time as last activity
$_SESSION['LAST_ACTIVITY'] = time();
?>