<?php


require_once __DIR__ . '/config.php';
require_once("validation");

class Database
{

    // FIXME: put this in a config and refer to that instead. 
    // private $host = '127.0.0.1';
    // private $dbname = "test_database";
    // private $dbuser = "test_user";
    // private $dbpass = "strong_password";
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

    public function subjectsFetchAll($subject_code) : array
    {
        /*KOPIERT FRA SUBJECT_MESSAGES AND THEREFORE DOES NOT WORK*/
        try {
            $stmt = $this->pdo->prepare(
                "SELECT Subject_ID, Subject_name  
                from subjects 
                where Subject_PIN = :subject_code"
            );

            $stmt->execute(
                [":subject_code" => $subject_code]
            );
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $subjects;
        } catch (PDOException $e) {
            die("Serious error message for serious problems" . $e->getMessage());
        }
    }

    public function subjectFetchData() {}


    public function subjectPinExists(string $subject_pin): bool
    {
        if (!validateSubjectPin($subject_pin)) {
            return false;
        } else {
            try {
                $stmt = $this->pdo->prepare(
                    "SELECT subject, subject_pin 
                    FROM register 
                    WHERE subject_pin = :subm_pin"
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

    public function subjectMessageSubmit(string $subject_code, string $new_message): bool
    {
        if (!(validateSubjectCode($subject_code) || validateFreetext($new_message)))
            try {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO mock_database(emne_id, message) 
                    VALUES (:subject_code, :new_message)"
                );

                return $stmt->execute([
                    ":subject_code" => $subject_code,
                    ":new_message" => $new_message
                ]);
            } catch (PDOException $e) {
                die($this->ERROR_MSG . $e->getMessage());
            }
        return false;
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
                    "INSERT INTO users (username, email, password, subject, subject_pin, subject_code)
                    VALUES (:username, :email, :password, :subject, :subject_pin, :subject_code)"
                    //ignoring "picture filename", "subject_ID", "subject pin" and "session_cookie"
                );

                $stmt->execute([
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $password,
                    ":subject" => $subject,
                    ":subject_pin" => $pin,
                    ":subject_code" => $subject_code,
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
                    "INSERT INTO users (username, email, password, subject, subject_pin, subject_code)
                    VALUES (:username, :email, :password, :subject, :subject_pin, :subject_code)"
                );

                $stmt->execute([
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $password,
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
        try {
            $stmt = $this->pdo->prepare("SELECT id, password, role FROM t_users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $e) {
            // NOTE: What even causes this, when the statement is fucked or when it doesn't find anything?
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
