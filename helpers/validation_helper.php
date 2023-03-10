<?php
require_once "http_status_helper.php";

function getRegistrationValidationResult($requestData): bool
{
    $fullName = $requestData->body->fullName ?? null;
    $password = $requestData->body->password ?? null;
    $email = $requestData->body->email ?? null;
    $birthdate = $requestData->body->birthDate ?? null;
    $gender = $requestData->body->gender ?? null;
    $phoneNumber = $requestData->body->phoneNumber ?? null;

    if (!checkFullnameValidity($fullName)) {
        setHttpStatus("400", "Fullname must contain only letters and consist minimum of at least 2 words");
        return false;
    } else if (!checkPasswordValidity($password)) {
        setHttpStatus("400", "Password must contain at least 6 characters and at least: one uppercase letter, one lowercase letter, one digit, one special symbol");
        return false;
    } else if (!checkEmailValidity($email)) {
        setHttpStatus("400", "Email must be in format example@example.com");
        return false;
    } else if (!checkDateValidity($birthdate, true)) {
        setHttpStatus("400", "Date must be empty/null or in correct format (YYYY-MM-DD / YYYY-MM-DDTHH:MM:SS / YYYY-MM-DDTHH:MM:SS.vP) and lie in the range of 01.01.1900 and our time");
        return false;
    } else if (!checkGenderValidity($gender)) {
        setHttpStatus("400", "Gender must be Male or Female");
        return false;
    } else if (!checkPhoneNumberValidity($phoneNumber)) {
        setHttpStatus("400", "Phone must be empty/null or in correct form");
        return false;
    } else {
        return true;
    }
}

function getProfileDataValidationResult($requestData): bool
{
    $fullName = $requestData->body->fullName ?? null;
    $birthdate = $requestData->body->birthDate ?? null;
    $gender = $requestData->body->gender ?? null;
    $phoneNumber = $requestData->body->phoneNumber ?? null;

    if (!checkFullnameValidity($fullName)) {
        setHttpStatus("400", "Fullname must contain only letters and consist minimum of at least 2 words");
        return false;
    } else if (!checkGenderValidity($gender)) {
        setHttpStatus("400", "Gender must be Male or Female");
        return false;
    } else if (!checkDateValidity($birthdate, true)) {
        setHttpStatus("400", "Date must be empty/null or in correct format (YYYY-MM-DD / YYYY-MM-DDTHH:MM:SS / YYYY-MM-DDTHH:MM:SS.vP) and lie in the range of 01.01.1900 and our time");
        return false;
    } else if (!checkPhoneNumberValidity($phoneNumber)) {
        setHttpStatus("400", "Phone must be empty/null or in correct form");
        return false;
    } else {
        return true;
    }
}

function getOrderValidationError($requestData): bool
{
    $deliveryTime = $requestData->body->deliveryTime ?? null;
    $address = $requestData->body->address ?? null;

    if (!checkDateValidity($deliveryTime, false)) {
        setHttpStatus("400", "deliveryTime mustn't be null/empty and must be in correct format (YYYY-MM-DDTHH:MM:SS / YYYY-MM-DDTHH:MM:SS.vP) and at least an hour greater than orderTime");
        return false;
    } else if (!checkAddressValidity($address)) {
        setHttpStatus("400", "address is invalid");
        return false;
    } else {
        return true;
    }
}

function checkAddressValidity($address): bool
{
    return $address != null and $address != "";
}

function checkFullnameValidity($fullname): bool
{
    $regex = "/^[a-zA-Z??-??]+([-']?[a-zA-Z??-??]]+)*(\s[a-zA-Z??-??]([-']?[a-zA-Z??-??]+)*)+$/ui";
    if (preg_match($regex, $fullname)) {
        return true;
    } else {
        return false;
    }
}

function checkDateValidity($date, $isDateBirthday): bool
{
    $format = 'Y-m-d\TH:i:s';
    $secondFormat = 'Y-m-d';
    if ($date == null) {
        if ($isDateBirthday) {
            return true;
        } else {
            return false;
        }
    } else if ($date == "") {
        if ($isDateBirthday) {
            return true;
        } else {
            return false;
        }
    } else {
        $d = DateTime::createFromFormat($format, $date);
        $d2 = DateTime::createFromFormat($secondFormat, $date);

        if ($d && $d->format($format) == $date) {
            if ($isDateBirthday) {
                return checkBirthDateRangeValidity($d);
            } else {
                return checkDeliveryDateValidity($d);
            }
        } else if ($d2 && $d2->format($secondFormat) == $date) {
            if ($isDateBirthday) {
                return checkBirthDateRangeValidity($d2);
            } else {
                return false;
            }
        } else {
            $d3 = DateTime::createFromFormat(DATE_RFC3339_EXTENDED, $date);
            if ($d3) {
                $d3->setTimezone(new DateTimeZone("Asia/Krasnoyarsk"));
                $d3->modify("-7 hours");
                $datePart = explode("+", $d3->format(DATE_RFC3339_EXTENDED))[0] . "Z";
                if ($datePart == $date) {
                    if ($isDateBirthday) {
                        return checkBirthDateRangeValidity($d3);
                    } else {
                        return checkDeliveryDateValidity($d3);
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}

function checkBirthDateRangeValidity($date): bool
{
    $nowadays = new DateTime();
    $oldDate = new DateTime("01/01/1900");
    return $date >= $oldDate && $date < $nowadays;
}

function checkDeliveryDateValidity($date): bool
{
    $nowadays = new DateTime();
    $nowadays->modify('+1 hour');
    return $date >= $nowadays;
}


function checkPhoneNumberValidity($phoneNumber): bool
{
    if ($phoneNumber == null) {
        return true;
    } else if (strlen($phoneNumber) == "") {
        return true;
    } else {
        $regex = '/^((\+7|7|8)+([0-9]){10})$/m';
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

function convertDateToCorrectForm($birthDate): string
{
    $result = str_replace(' ', 'T', $birthDate);
    $part = explode(".", $result)[1] ?? null;
    if ($part == null) {
        $result = $result . ".000Z";
    } else {
        if (strlen($part) == 3) {
            $result = $result . "Z";
        } else if (strlen($part) == 2) {
            $result = $result . "0Z";
        } else if (strlen($part) == 1) {
            $result = $result . "00Z";
        }
    }
//    echo json_encode($result) . PHP_EOL;

    return $result;
}