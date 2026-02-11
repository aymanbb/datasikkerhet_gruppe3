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
                "SELECT subject_name  
                from users 
                where is_teacher = true"
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
                "CALL addMessage (:user_id , :message_body, :message_subject);"
            );

            return $stmt->execute([
                ":user_id" => $user_id,
                ":message_subject" => $subject_pin,
                ":message_body" => $message_body
            ]);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return true;
    }
    public function subjectMessageAnswerSubmit(int $message_id, string $answer_text): array
    {
        if (!validateFreetext($message_body)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "CALL addAnswerToMessage (:message_id, :answer);" 
            );

            $success = $stmt->execute(
                [
                    ":message_id" => $message_id,
                    ":answer" => $answer_text,
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
                "SELECT subject_pin, message_body
                FROM messages 
                WHERE subject_pin = :subject_pin"
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
                "SELECT comment_id, comment_body 
                FROM comments 
                WHERE message_id = :message_id"
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

    public function userLecturerRegister($username, $email, $password, $image, $subject, $pin): bool
    {
        // Alle disse feltene må være valid
        if (!(validateUsername($username) || validateEmail($email) || validatePassword($password) || validateSubject($subject) || validateSubjectPin($pin))) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "CALL addTeacher (:username, :email, :password, :image, :subject, :pin);"
                );

                $stmt->execute([
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $password,
                    ":image" => $image,
                    ":subject" => $subject,
                    ":pin" => $pin
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
                    "CALL addStudent (:name, :email, :password);"
                );

                $stmt->execute([
                    ":name" => $username,
                    ":email" => $email,
                    ":password" => $password,
                ]);
                echo "success";
                return true;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Username or email already exists.";
                } else {
                    $message = "An error occurred.";
                    echo $e;
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
                "SELECT user_id, password, email 
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
