<?php
header("Content-Type: application/json");
// Always return JSON
require_once(__DIR__ . "/../includes/login.php");
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
$login = new Login();


$action = $data['action'] ?? '';
if ($action === 'login') {
    $username = $data["username"] ?? '';
    $password = $data["password"] ?? '';
    $success = $login->api_login($username, $password);
    if ($success){
        http_response_code(200);
        echo json_encode([
            "session_id" => session_id()
        ]);
        
    }else{
        
        http_response_code(400);
    }
    exit;    
}elseif($action === 'user_student_register'){
    $username = ($data['username']) ?? '';
    $email = ($data['email']) ?? '';
    $password = ($data['password']) ?? '';
    $success = $db->userStudentRegister($username, $email, $password);

    $request_status = $success ? "user register successful" : "user register not successful";
    http_response_code(200);
    echo json_encode([
        "status" => $request_status
    ]);
    exit;
}
// NOTE: You should only be able to get here if you are authorized....
// Let's see if that's the case..

// Extract session ID from Authorization header
$headers = getallheaders();
if (!empty($headers['Authorization'])) {
    if (preg_match('/Session\s(\S+)/', $headers['Authorization'], $matches)) {
        session_id($matches[1]);  // MUST happen before session_start()
    }
    else{
        http_response_code(400);
        exit;
    }
}
session_start();

// Example: simple action switch
switch ($action) {
    case "ping":
        echo json_encode([
            "status" => "success",
            "message" => "pong"
        ]);
        break;

    // case "login":
    //     $username = trim($_POST["login_username"]);
    //     $password = $_POST["login_password"];
    //     $login->login($username, $password);
    //     http_response_code(200);
    //     break;

    // case "user_student_register":
    //     $username = ($data['username']) ?? '';
    //     $email = ($data['email']) ?? '';
    //     $password = ($data['password']) ?? '';
    //     $success = $db->userStudentRegister($username, $email, $password);

    //     $request_status = $success ? "user register successful" : "user register not successful";
    //     http_response_code(200);
    //     echo json_encode([
    //         "status" => $request_status
    //     ]);
    //     break;

    case "subjects_fetch_all":
        $subjects = $db->subjectsFetchAll();
        http_response_code(200);
        echo json_encode([
            "data" => $subjects
        ]);
        break;

    case "subject_message_submit":
        $subject_pin = ($data['subject_pin']) ?? '';
        if (empty($subject_pin)) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }
        $status = $db->subjectMessageSubmit((int)$user_id, (int)$subject_pin, $message_body);
        http_response_code(200);
        echo json_encode([
            "status" => $status
        ]);
        break;

    case "subject_message_answer_submit":
        $subject_pin = ($data['subject_pin']) ?? '';
        $status = $db->subjectMessageAnswerSubmit((int)$message_id, $answer_text);
        http_response_code(200);
        echo json_encode([
            "status" => $status
        ]);
        break;

    case "subject_message_fetch_all":
        $subject_pin = ($data['subject_pin']) ?? '';
        if (empty($subject_pin)) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }
        $messages = $db->subjectMessageFetchAll((int)$subject_pin);
        http_response_code(200);
        echo json_encode([
            "data" => $messages
        ]);
        break;

    case "subject_message_comment_fetch_all":

        $message_id = ($data['message_id']) ?? '';
        if (empty($message_id)) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }
        $message_comments = $db->MessageCommentsFetchAll((int)$message_id);

        http_response_code(200);
        echo json_encode([
            "data" => $message_comments
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Unknown action or just a bad move in general."]);
}
exit;
