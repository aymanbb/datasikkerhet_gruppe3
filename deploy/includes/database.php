<?php

require_once __DIR__ . '/config.php';
require_once("validation");

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
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function subjectsFetchAll() : array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT Subject_ID, Subject_name  
                from users 
                where Is_teacher = true"
            );

            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $subjects;
        } catch (PDOException $e) {
            die("Serious error message for serious problems" . $e->getMessage());
        }
    }

    // get associated data for subject?
    // public function subjectFetchData(int $subject_pin) {
    //     return null;
    // }


    public function subjectPinExists(string $subject_pin): bool
    {
        if (!validateSubjectPin($subject_pin)) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT subject_pin 
                    FROM users 
                    WHERE subject_pin = :subm_pin
                    LIMIT 1"
                );

                $stmt->execute(
                    [":subm_pin" => $subject_pin]
                );

                $subject_match = $stmt->fetch();

                return $subject_match['subject_pin'] != null;
            } catch (PDOException $e) {
                die($this->ERROR_MSG . $e->getMessage());
            }
        }
    }

    public function subjectMessageSubmit(int $user_id, string $subject_code, string $message_body): bool
    {
        if (!(validateSubjectCode($subject_code) || validateFreetext($message_body)))
            try {
                $stmt = $this->pdo->prepare(
                    "addMessage (:message_User_ID , :message_message_body, :message_subject);"
                );

                return $stmt->execute([
                    ":message_User_ID" => $user_id,
                    ":message_subject" => $subject_code,
                    ":message_body" => $message_body
                ]);
            } catch (PDOException $e) {
                die($this->ERROR_MSG . $e->getMessage());
            }
        return false;
    }
    public function subjectMessageAnswerSubmit(int $message_id, string $message_body): array
    {
        if (!validateFreetext($message_body)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "addAnswerToMessage (:messageID, :answerText);" 
            );

            $success = $stmt->execute(
                [
                    ":messageID" => $message_id,
                    ":answerText" => $message_body,
                ]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die($this->ERROR_MSG . $e->getMessage());
        }
    }

    public function subjectMessageFetchAll(string $subject_code): array
    {
        if (!validateSubjectCode($subject_code)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT emne_id, message 
                FROM mock_database 
                WHERE emne_id = :subject_code"
            );

            $success = $stmt->execute(
                [":subject_code" => $subject_code]
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die($this->ERROR_MSG . $e->getMessage());
        }
    }

    public function userLecturerRegister($username, $email, $password, $subject, $subject_code, $pin, $image): bool
    {
        // Alle disse feltene må være valid
        if (!(validateUsername($username) || validateEmail($email) || validatePassword($password) || validateSubject($subject) || validateSubjectCode($subject_code) || validateSubjectPin($pin))) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "addTeacher (:teacher_name, :teacher_email, :teacher_password, :teacher_subject, :teacher_subjectPIN, :teacher_picFilename);"
                );

                $stmt->execute([
                    ":teacher_name" => $username,
                    ":teacher_email" => $email,
                    ":teacher_password" => $password,
                    ":teacher_subject" => $subject,
                    ":teacher_subjectPin" => $pin,
                    ":teacher_subject_code" => $subject_code,
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
                    "addStudent (:student_name, :student_email, :student_password);"
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
                "SELECT id, password, role 
                FROM users 
                WHERE username = :username"
                );

            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            // NOTE: What even causes this, when the statement is fucked or when it doesn't find anything?
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
