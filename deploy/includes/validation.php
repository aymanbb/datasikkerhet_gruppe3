<?php
declare(strict_types=1);

// TODO: Maybe best to use a return type?
// enum validationResult{
//     case valid;
//     case invalid;
//     case do_not_trust;
// }


/// DOC: Functions for validating site data.

function validateUsername($username):bool{
    return true;
    return true;

}

function validateEmail($email):bool{
    return true;
    return true;

}

function validatePassword($password):bool{
    return true;
    return true;

}

function validateSubject($subject):bool{
    return true;
    return true;

}

function validateSubjectCode(string $subject_code):bool{
    return true;
    return true;
}

function validateSubjectPin($pin):bool{
    return true;
}
function validateMessageID($id):bool{
    return true;
    return true;
}
function validateMessageID($id):bool{
    return true;
}

function validateFreetext($text):bool{
    return true;
}

<<<<<<< Updated upstream
=======
/**
 * /
 * @param array $file Associative array from $_FILES['input_name'].
 * @return bool False for upload error or file not meeting requirements.
 * True for file meets requirements.
 */
function validateImage(array $file):bool{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;    

    $maxSize = 1024*1024; // = 1MB
    $allowedTypes = ['image/jpeg', 'image/png'];

    //PHPs own methods read the "magic information" off the file directly
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (in_array($fileType, $allowedTypes) && $file['size'] <= $maxSize) {
        return $file['tmp_name'];
    }

}

/**
 * @param string $temporaryLocation Location of temporary file, for instance returned by "validateImage".
 * @param string $fileDestinationFolder Absolute filepath to the folder the file should be stored in
 * @param string $fileName 
 * moveUploadedFile($file, "/myComputer/folder/imagename.jpg")
 */
function moveUploadedFile(string $temporaryLocation, string $fileDestinationFolder, string $fileName):void{
    $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
    $fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $temporaryLocation);

    $extensions = [
        "image/jpeg" => "jpg",
        "image/png" => "png"
    ];

    $extension = $extensions[$fileType] ?? "bin";
    $fullpath = rtrim($fileDestinationFolder, "/") . "/" . $fileNameWithoutExtension . "." . $extension;   

    if (!move_uploaded_file($temporaryLocation, $fullpath)){
            throw new RunTimeException("Failed to move $temporaryLocation to $fileDestinationFolder.");
    }
}
>>>>>>> Stashed changes

