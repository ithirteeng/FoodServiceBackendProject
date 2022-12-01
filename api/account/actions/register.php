<?php

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}


function postData($requestData): void
{
    $fullName = $requestData->body->fullName;
    $password = hash("sha1", $requestData->body->password);
    $address = $requestData->body->address;
    $email = $requestData->body->email;
    $birthdate = ;
    $gender = $requestData->body->gender;
    //echo "postRegister";
}