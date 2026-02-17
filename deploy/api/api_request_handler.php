<?php
header("Content-Type: application/json");
// Always return JSON

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

//TODO: check credentials?

/*
Simple API

We would have to use curl, through a commandline, to test the API. 

\ is simply a line break in the command
-H is the header of the post request
-d is the data, in the form of json, that needs to be sent

-d should contain:
    - An "action" field referring to the function that is being called
    - A field for each of the required parameters of the function call.
Any extra data there will be discarded.

Here is an example:

curl -X POST http://158.39.188.219/api/api_request_handler.php \
  -H "Content-Type: application/json" \
  -d '{
        "session_id": "abc123",
        "action": "user_student_register",
        "username": "name nameson",
        "email": "email@email.com",
        "password": "hunter2"
      }'



In order to do anything, you have to authenticate in some way....allegedly.
*/ 

// Example: simple action switch
$action = $data['action'] ?? '';

switch ($action) {
    case "ping":
        echo json_encode([
            "status" => "success",
            "message" => "pong"
        ]);
        break;

    case "login":
        break;

    case "user_student_register":
        $username = ($data['username']) ?? '';
        $email = ($data['email']) ?? '';
        $password = ($data['password']) ?? '';
        $success = $db->userStudentRegister($username, $email, $password);

        $request_status = $success ? "user register successful" : "user register not successful";
        http_response_code(200);
        echo json_encode([
            "status" => $request_status
        ]);
        break;
        
    case "subjects_fetch_all":
        $subjects = $db->subjectsFetchAll();
        http_response_code(200);
        echo json_encode([
            "data" => $subjects
        ]);
        break;

    case "subject_message_submit":
        $subject_pin = ($data['subject_pin']) ?? '';
        if(empty($subject_pin)){
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
        if(empty($subject_pin)){
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
        if(empty($message_id)){
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
        echo json_encode(["error" => "Unknown action"]);
}
exit;
?>