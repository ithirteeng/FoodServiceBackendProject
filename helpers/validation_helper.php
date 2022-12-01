<?php
require_once "http_status_helper.php";

function getRegistrationValidationResult($requestData): bool
{
    $fullName = $requestData->body->fullName;
    $password = $requestData->body->password;
    $address = $requestData->body->address;
    $email = $requestData->body->email;
    $birthdate = $requestData->body->birthDate;
    $gender = $requestData->body->gender;
    $phoneNumber = $requestData->body->phoneNumber;

    if (!checkFullnameValidity($fullName)) {
        setHttpStatus("415", "Fullname must contain only letters and consist of at least 2 words");
        return false;
    } else if (checkPasswordValidity($password)) {
        setHttpStatus("415", "Password must contain at least 6 characters and: \none uppercase letter,
         \none lowercase letter, 
         \none digit, 
         \n one special symbol");
        return false;
    } else if (!checkEmailValidity($email)) {
        setHttpStatus("415", "Email must be in format example@example.com");
        return false;
    } else if (!checkDateValidity($birthdate)) {
        setHttpStatus("415", "Date must be in format YYYY-MM-DD");
        return false;
    } else if (!checkGenderValidity($gender)) {
        setHttpStatus("415", "Gender must be Male or Female");
        return false;
    } else if (checkPhoneNumberValidity($phoneNumber)) {
        setHttpStatus("415", "Phone must be empty or in correct form");
        return false;
    } else {
        return true;
    }
}

function checkFullnameValidity($fullname): bool
{
    $regex = "/^[a-z]([-']?[a-z]+)*( [a-z]([-']?[a-z]+)*)+$/i";
    $regex2 = "/^[а-я]([-']?[а-я]+)*( [а-я]([-']?[а-я]+)*)+$/i";
    return (preg_match($regex) or preg_match($regex2)) and strlen($fullname) > 1;
}

function checkDateValidity($date, $format = 'Y-m-d'): bool
{
    if ($date == null) {
        return true;
    } else if ($date == "") {
        return true;
    } else {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

function checkPhoneNumberValidity($phoneNumber): bool
{
    $regex = '/^((\+7|7|8)+([0-9]){10})$/m';
    if ($phoneNumber == null) {
        return true;
    } else if (strlen($phoneNumber) == "") {
        return true;
    } else {
        return preg_match($regex, $phoneNumber);
    }
}

function checkPasswordValidity($password): bool
{
    $regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}$/';
    // at least one digit, upper case letter, lower case letter and one special character (len > 6)
    return preg_match($regex, $password);
}

function checkEmailValidity($email): bool
{
    $regex = '/^[A-Z0-9._%+-]+@[A-Z0-9-]+.+\.[A-Z]{2,4}$/i';
    return preg_match($regex, $email) and strlen($email) > 0;
}

function checkGenderValidity($gender): bool
{
    return ($gender == 'Male' or $gender == 'Female') and strlen($gender) >= 4;
}