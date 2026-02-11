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

}

function validateEmail($email):bool{
    return true;

}

function validatePassword($password):bool{
    return true;

}

function validateSubject($subject):bool{
    return true;

}

function validateSubjectCode(string $subject_code):bool{
    return true;
}

function validateSubjectPin($pin):bool{
    return true;
}
function validateMessageID($id):bool{
    return true;
}

function validateFreetext($text):bool{
    return true;
}

/**
 * /
 * @param array $file Associative array from $_FILES['input_name'].
 * @return string The temporary file path, if validation passes.
 * @throws RuntimeException If the PHP upload error code is not UPLOAD_ERR_OK.
 * @throws UnexpectedValueException If the MIME type is not permitted.
 * @throws LengthException If the file exceeds $maxSize.
 */
function validateImage(array $file):string{
    if ($file['error'] !== UPLOAD_ERR_OK){
        throw new RuntimeException("Upload failed with error code: " . $file['error']);
    }
    $maxSize = 1024*1024; // = 1MB
    $allowedTypes = ['image/jpeg', 'image/png'];

    //PHPs own methods read the "magic information" off the file directly
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($fileType, $allowedTypes)) {
        //does not display the incorrect filetype to avoid educating attackers
        throw new UnexpectedValueException("Invalid file type.");
    }
    if ($file['size'] > $maxSize) {
        throw new LengthException("File exceeds maximum size.");
    }

    return $file['tmp_name'];
}

/**
 * @param string $temporaryLocation Path to the validated temporary file, for instance returned by "validateImage".
 * @param string $fileDestinationFolder Directory where the file will be stored.
 * @param string $fileName Original name of the file.
 * @throws RuntimeException If the file cannot be moved to the destination folder.
 */
function moveUploadedFile(string $temporaryLocation, string $fileDestinationFolder, string $fileName):void{
    $fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $temporaryLocation);
    $extensions = [
        "image/jpeg" => "jpg",
        "image/png" => "png"
    ];
    $extension = $extensions[$fileType] ?? "bin";

     //prevets duplicates and sanitizes - hash based on the file's binary data
    $uniqueName = md5_file($temporaryLocation);

    $fullpath = rtrim($fileDestinationFolder, "/") . "/" . $uniqueName . "." . $extension;   

    //does not throw an exception to allow the user to continue. Should be logged if invoked, likely malicious.
    if (file_exists($fullpath)) return;
    if (!move_uploaded_file($temporaryLocation, $fullpath)){
            throw new RuntimeException("Failed to move $temporaryLocation to $fileDestinationFolder.");
    }
}

