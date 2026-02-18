<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/database.php';
    require_once __DIR__ . '/includes/session.php';

    //$form id = $_POST["form name"] -> id og name fra foreleser_register.html
    $username = $_POST["register_username"];
    $email = $_POST["register_email"];
    $password = $_POST ["register_password"];
    $subject_name = $_POST["subject_name"];
    $subject_pin = $_POST["subject_pin"];
    $image = $_FILES["foreleser_image"];

    // $sql = "insert into users (user_id, username, email, password, is_teacher, picture_filename, subject_pin, subject_name)  values ('" . $registerUsername . "', '" . $registerEmail . "', '" . $registerPassword . "', '" . $subjectName . "', '" . $subjectPin . "', '" . $foreleserImage . '");");

    $db = new Database();

    $message = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if(isset($_POST['foreleser_register_submit'])) {

            $username = trim($_POST["register_username"]);
            $email = trim(string: $_POST["register_email"]);
            $password = password_hash($_POST["register_password"], PASSWORD_DEFAULT);
            $subject = trim($_POST["register_subject"]);
            $pin = $_POST["register_pin"];
            $image = $_FILES["register_image"];

            if (empty($username) || empty($email) || empty($password) || empty($subject) || empty($pin) || empty($image)) {
                $message = "All fields are required.";
            } else {

                try {
                // Image handling
                    $image = "NONE";
                    $GLOBALS['image'] = $_FILES['register_image']['name'];
                    $imageName = $_FILES['register_image']['name'];
                    $allowed_extensions = array('jpg', 'png');

                    // Retrieve the extension using the image file name
                    $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

                    if (in_array($extension, $allowed_extensions)) {
                        // Assuming $tempname is meant to be the temporary file path
                        $tempname = $_FILES['register_image']['tmp_name'];

                        // Move the uploaded file
                        move_uploaded_file($tempname, "/var/www/html/media/" . $GLOBALS['image']);
                    } else {
                        echo "Error: File extension not allowed. " . $extension;
                    }
                    //Bildehåndtering slutt

                    $check_name = $db->userFindByUsername($username);
                    $check_subject = $db->findSubjectByName($subject);

                    if ($check_name == null && $check_subject == null) {
                        if($db->userLecturerRegister($username, $email, $password, $image, $pin, $subject)){
                        //FIXME: some error message here
                            echo "success";
                        } else {
                        // FIXME: Some error message here
                            echo "fail";
                        }
                    } else {
                        echo "Username or subject already exists!";
                    }

                    $message = "Registration successfull!";
                    } catch (PDOException $e) {
                         echo $e->getMessage();
                        if ($e->getCode() == 23000) {
                            $message = "Username or email already exists.";
                        } else {
                            $message = "An error occurred.";
                        }
                    }
                }
            }
        }
?>