<?php
// Always return JSON
header("Content-Type: application/json");

require_once(__DIR__ . "/../includes/validation.php");
require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/database.php");

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Get raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$db = new Database();

// Example: simple action switch
$action = $data['action'] ?? '';

switch ($action) {
    case "ping":
        echo json_encode([
            "status" => "success",
            "message" => "pong"
        ]);
        break;

    case "register_student":
        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $success = $db->userStudentRegister($username, $email, $password);

        $request_status = $success ? "user register successful" : "user register not successful";
        echo json_encode([
            "status" => $request_status
        ]);
        exit;
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Unknown action"]);
}