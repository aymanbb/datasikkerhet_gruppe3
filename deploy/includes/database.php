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
                "SELECT subject_name FROM users WHERE is_teacher = true"
            );

            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $subjects;
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return [];
    }

    public function subjectPinExists(int $subject_pin): bool
    {
        if (validateSubjectPin($subject_pin)) {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT subject_pin FROM users WHERE subject_pin = :subm_pin LIMIT 1"
                );

                $stmt->execute(
                    [":subm_pin" => $subject_pin]
                );

                $subject_match = $stmt->fetch(PDO::FETCH_ASSOC);

                return !empty($subject_match['subject_pin']);
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
                "CALL addMessage (:message_user_id, :message_body, :subject_pin);"
            );

            return $stmt->execute([
                ":message_user_id" => $user_id,
                ":message_body" => $message_body,
                ":subject_pin" => $subject_pin
            ]);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
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
                "CALL addAnswerToMessage (:message_id, :answer);"
            );

            $stmt->execute(
                [
                    ":message_id" => $message_id,
                    ":answer" => $message_body,
                ]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }

    public function subjectMessageFetchAll(int $subject_pin): array
    {
        if (!validateSubjectPin($subject_pin)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT subject_pin, message_body FROM messages WHERE subject_pin = :subject_pin"
            );

            $stmt->execute([":subject_pin" => $subject_pin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }
    public function MessageCommentsFetchAll(int $message_id): array
    {
        if (!validateMessageID($message_id)) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT comment_id, comment_body FROM comments WHERE message_id = :message_id"
            );

            $stmt->execute([":message_id" => $message_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
            return [];
        }
    }

    public function userLecturerRegister($username, $email, $password, $image, $subject, $pin): bool
    {
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
                return false;
            }
        }
    }

    public function userStudentRegister($username, $email, $password): bool
    {
        if (!(validateUsername($username) || validateEmail($email) || validatePassword($password))) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "CALL addStudent (:student_name, :student_email, :student_password);"
                );

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt->execute([
                    ":student_name" => $username,
                    ":student_email" => $email,
                    ":student_password" => $hash,
                ]);
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    // Normalize returned user data: id (int), username, password (when requested)
    public function userFindByUsername(string $username)
    {
        if (!validateUsername($username)){
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT user_id AS id, username, password FROM users WHERE username = :username LIMIT 1"
            );

            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) return null;

            $user['id'] = isset($user['id']) ? (int)$user['id'] : null;

            return $user;
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return null;
    }

    private function panic(string $file, int $line, PDOException $error){
        die($this->ERROR_MSG . "\n" . $error->getMessage() . "- in ". $file . ":" . $line);
    }

    public function userFindByEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT user_id AS id, username FROM users WHERE email = :email LIMIT 1"
            );

            $stmt->execute([':email' => $email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) return null;

            $user['id'] = isset($user['id']) ? (int)$user['id'] : null;

            return $user;
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return null;
    }

    public function resetPassword(string $username, string $new_password): bool
    {
        if (!validateUsername($username)) {
           return false;
        }

        try {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE username = :username");

            $stmt->execute([
                    ':password' => $hash,
                    ':username' => $username
                    ]);

            return ($stmt->rowCount() > 0);
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__, $e);
            return false;
        }
    }

    public function findUserId(string $username)
    {
        if (!validateUsername($username)){
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT user_id FROM users WHERE username = :username LIMIT 1"
            );

            $stmt->execute([':username' => $username]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) return null;

            return (int)$row['user_id'];
        } catch (PDOException $e) {
            $this->panic(__FILE__, __LINE__,$e);
        }
        return null;
    }
}