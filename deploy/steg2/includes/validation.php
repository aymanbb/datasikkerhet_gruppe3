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
}

function validateFreetext($text):bool{
    return true;
}

/**
 * Verifies that the uploaded file meets size requirements, matches allowed MIME types and is uploaded without error.
 * @param array $file Associative array from $_FILES['input_name'].
 * @return string The temporary filepath, if validation passes.
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

    //PHPs own methods read the "magic information" off the file directly.
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($fileType, $allowedTypes)) {
        //does not display the incorrect filetype to avoid educating attackers.
        throw new UnexpectedValueException("Invalid file type.");
    }
    if ($file['size'] > $maxSize) {
        throw new LengthException("File exceeds maximum size.");
    }
    return $file['tmp_name'];
} 

/**
 * Generates a unique filename by making a content hash, moves the file to designated storage.
 * @param string $temporaryLocation Path to the temporary file, returned by validateImage().
 * @param string $fileDestinationFolder Target directory for storage.
 * @throws RuntimeException If the file cannot be moved to target destination.
 */
function moveUploadedFile(string $temporaryLocation, string $fileDestinationFolder):void{

    $fileType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $temporaryLocation);
    $extensions = [
        "image/jpeg" => "jpg",
        "image/png" => "png"
    ];
    //TODO: decide: Will this allow .exe, for instance ? should it also be disallowed here?
    $extension = $extensions[$fileType] ?? "bin";

     //prevets duplicates and sanitizes - hash based on the file's binary data
    $uniqueName = md5_file($temporaryLocation);
    $fullpath = rtrim($fileDestinationFolder, "/") . "/" . $uniqueName . "." . $extension;  

    if (file_exists($fullpath)) return;
    if (!move_uploaded_file($temporaryLocation, $fullpath)){
            throw new RuntimeException("Failed to move $temporaryLocation to $fileDestinationFolder.");
    }

} 

