<?php

require_once "helpers/validation_helper.php";
require_once "helpers/http_status_helper.php";
require_once "helpers/jwt_helper.php";

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}


function postData($requestData): void
{
    global $link;
    $email = $requestData->body->email;
    if (pg_fetch_assoc(
        pg_query($link, "select email from users where email = '$email'")
    )) {
        setHttpStatus("409", "User already exists!");
    } else {
        $fullName = $requestData->body->fullName;
        $password = $requestData->body->password;
        $address = $requestData->body->address;
        $birthdate = $requestData->body->birthDate;
        $gender = $requestData->body->gender;
        $phoneNumber = $requestData->body->phoneNumber;
        $hashPassword = hash("sha1", $password);
        if (getRegistrationValidationResult($requestData)) {
            pg_query($link, "insert into users (fullname, email, address, birthdate, phonenumber, password, gender)
                                    values ('$fullName', '$email', '$address', '$birthdate', '$phoneNumber', '$hashPassword', '$gender')");

            $newToken = createToken($email);
            if (checkIfTokenInBlackList($newToken)) {
                pg_query($link, "delete from token_blacklist where value = '$newToken'");
            }

            echo json_encode(["token" => $newToken]);
        }
    }
}