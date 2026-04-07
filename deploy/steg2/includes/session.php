<?php
require_once __DIR__ . '/global_delay.php'

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_name('session');
    session_start();
}

// Session timeout after 30 minutes
$sessionLifetime = 1800; // seconds

// Initialize rate limiter variables
$maxAttempts = 5;   // Maximum attempts
$lockoutTime = 900; // Lockout period in seconds (15 minutes)


// Record of last activity to handle session expiration
if (
    isset($_SESSION['LAST_ACTIVITY']) &&
    (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)
) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Store the current time as last activity
$_SESSION['LAST_ACTIVITY'] = time();

// Function to reset login attempts
function resetAttempts()
{
    global $lockoutTime;

    if (isset($_SESSION['last_attempt_time'])) {
        $elapsedTime = time() - $_SESSION['last_attempt_time'];
        if ($elapsedTime >= $lockoutTime) {
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['last_attempt_time']);
        }
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper to output CSRF field in forms.
function csrf_field(): string
{
    $token = $_SESSION['csrf_token'] ?? '';

    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars($token, ENT_QUOTES, 'UTF-8') .
        '">';
}


// Helper to validate CSRF token in POST handlers.
function validate_csrf_token(): void
{
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(400);
        die('Invalid CSRF token');
    }
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


function rate_limiter($key, $limit, $period)
{
    // Create a secure file name based on the key using SHA-256 hashing
    $filename = 'RLIMITER/' . hash('sha256', $key) . '.txt';

    // Get the IP address of the client, handling proxy headers if present
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Ensure the IP address is a valid IPv4 or IPv6 address
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
        die('Connection error');
    }

    // Initialize the data array
    $data = [];

    // Check if the file exists and read its contents
    if (file_exists($filename)) {
        $data = json_decode(file_get_contents($filename), true);
    }

    // Get the current time and reset the count if the period has elapsed
    $current_time = time();
    if (
        isset($data[$ip]) &&
        $current_time - $data[$ip]['last_access_time'] >= $period
    ) {
        $data[$ip]['count'] = 0;
    }

    // Check if the limit has been exceeded
    if (isset($data[$ip]) && $data[$ip]['count'] >= $limit) {
        http_response_code(429);
        header('Retry-After: ' . $period);
        die('Error: Rate limit exceeded');
    }

    // Increment the count and save the data to the file
    if (!isset($data[$ip])) {
        $data[$ip] = [
            'count'            => 0,
            'last_access_time' => 0,
        ];
    }

    $data[$ip]['count']++;
    $data[$ip]['last_access_time'] = $current_time;

    file_put_contents($filename, json_encode($data));

    // Return the remaining time until the limit resets (in seconds)
    return $period - ($current_time - $data[$ip]['last_access_time']);
}

?>
