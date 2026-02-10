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
    return false;

}

function validateEmail($email):bool{
    return false;

}

function validatePassword($password):bool{
    return false;

}

function validateSubject($subject):bool{
    return false;

}

function validateSubjectCode(string $subject_code):bool{
    return false;
}

function validateSubjectPin($pin):bool{
    return false;
}

function validateFreetext($text):bool{
    return false;
}

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
 * @param string $fileDestinationFolder Absolute filepath to permanent storage.
 * 
 * moveUploadedFile($file, "/myComputer/folder/imagename.jpg")
 */
function moveUploadedFile(string $temporaryLocation, string $fileDestinationFolder, string $fileName):void{
    $fileType = mime_content_type($temporaryLocation);
    $extension = explode("/", $fileType)[1] ?? "bin";
    $fullpath = rtrim($fileDestinationFolder, "/") . "/" . $fileName . "." . $extension;   

    if (!move_uploaded_file($temporaryLocation, $fullpath)){
            throw new RunTimeException("Failed to move $temporaryLocation to $fileDestinationFolder.");
    }
}

