<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/validation.php';

class Database
{
    private $pdo = null;
    private $ERROR_MSG = "Oopsie woopsie! UWU we made a fucky wucky!!\n";

    // Establishes a connection to database and constructs PDO
    function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
    }

    public function subjectsFetchAll() : array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT Subject_name  
                from users 
                where Is_teacher = true"
            );

            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $subjects;
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return [];
    }

    // get associated data for subject?
    // public function subjectFetchData(int $subject_pin) {
    //     return null;
    // }

    public function subjectPinExists(string $subject_pin): bool
    {
        if (validateSubjectPin($subject_pin)) {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT Subject_PIN 
                    FROM users 
                    WHERE Subject_PIN = :subm_pin
                    LIMIT 1"
                );

                $stmt->execute(
                    [":subm_pin" => $subject_pin]
                );

                $subject_match = $stmt->fetch();

                return $subject_match['subject_pin'] != null;
            } catch (PDOException $e) {
                $this->panic(__FILE__, __LINE__,$e);
            }
        }
        return false;
    }

    public function subjectMessageSubmit(int $user_id, int $subject_pin, string $message_body): bool
    {
        if (!(validateSubjectPin($subject_pin) || validateFreetext($message_body))){
            return false;
        }
        try {
            $stmt = $this->pdo->prepare(
                "CALL addMessage (:message_User_ID , :message_body, :message_subject);"
            );

            return $stmt->execute([
                ":message_User_ID" => $user_id,
                ":message_subject" => $subject_pin,
                ":message_body" => $message_body
            ]);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return true;
    }
    public function subjectMessageAnswerSubmit(int $message_id, string $message_body): array
    {
        if (!validateFreetext($message_body)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "CALL addAnswerToMessage (:messageID, :answerText);" 
            );

            $success = $stmt->execute(
                [
                    ":messageID" => $message_id,
                    ":answerText" => $message_body,
                ]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }

    public function subjectMessageFetchAll(string $subject_pin): array
    {
        if (!validateSubjectPin($subject_pin)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT Message_ID, Subject_PIN, Message_body
                FROM messages 
                WHERE Subject_PIN = :subject_pin"
            );

            $success = $stmt->execute(
                [":subject_pin" => $subject_pin]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }
    public function MessageCommentsFetchAll(string $message_id): array
    {
        if (!validateMessageID($message_id)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT Comment_ID, comment_body 
                FROM comments 
                WHERE Message_ID = :message_id"
            );

            $success = $stmt->execute(
                [":message_id" => $message_id]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }

    public function userLecturerRegister($username, $email, $password, $subject, $pin, $image): bool
    {
        // Alle disse feltene må være valid
        if (!(validateUsername($username) || validateEmail($email) || validatePassword($password) || validateSubject($subject) || validateSubjectPin($pin))) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "CALL addTeacher (:teacher_name, :teacher_email, :teacher_password, :teacher_subject, :teacher_subjectPIN, :teacher_picFilename);"
                );

                $stmt->execute([
                    ":teacher_name" => $username,
                    ":teacher_email" => $email,
                    ":teacher_password" => $password,
                    ":teacher_subject" => $subject,
                    ":teacher_subjectPin" => $pin,
                ]);

                return true;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Username or email already exists.";
                } else {
                    $message = "An error occurred.";
                }
                return false;
            }
        }
    }
    public function userStudentRegister($username, $email, $password): bool
    {
        // Alle disse feltene må være valid
        if (!(validateUsername($username) || validateEmail($email) || validatePassword($password))) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "CALL addStudent (:student_name, :student_email, :student_password);"
                );

                $stmt->execute([
                    ":student_username" => $username,
                    ":student_email" => $email,
                    ":student_password" => $password,
                ]);

                return true;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Username or email already exists.";
                } else {
                    $message = "An error occurred.";
                }
                return false;
            }
        }
    }

    public function userFindByUsername(string $username)
    {
        if (!validateUsername($username)){
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT User_ID, password, Email 
                FROM users 
                WHERE username = :username"
                );

            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            // NOTE: What even causes this, when the statement is fucked or when it doesn't find anything?
            $this->panic(__FILE__, __LINE__,$e);
        }
    }

    private function panic(string $file, int $line, PDOException $error){
        die($this->ERROR_MSG . "\n" . $error->getMessage() . "- in ". $file . ":" . $line);
    }
}
