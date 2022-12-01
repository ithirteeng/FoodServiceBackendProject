<?php

require_once "helpers/validation_helper.php";

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}


function postData($requestData): void
{
    $fullName = $requestData->body->fullName;
    $password = $requestData->body->password;
    $address = $requestData->body->address;
    $email = $requestData->body->email;
    $birthdate = $requestData->body->birthDate;
    $gender = $requestData->body->gender;
    $phoneNumber = $requestData->body->phoneNumber;

    if (getRegistrationValidationResult($requestData)) {
        echo "valid";
    } else {
        echo "\ninvalid";
    }

}