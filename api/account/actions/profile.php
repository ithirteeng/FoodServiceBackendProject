<?php

require_once "helpers/jwt_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/http_status_helper.php";
require_once "helpers/validation_helper.php";

function checkRequestMethods($method): void
{
    if ($method != "GET" && $method != "PUT") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function getData(): void
{
    global $link;

    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("401", "User is unauthorized");
    } else {
        $email = getEmailFromToken($token);
        $profileData = pg_query($link, "select * from users where email = '$email'");
        $responseData = [];
        while ($tableRow = pg_fetch_assoc($profileData)) {
            $responseData = [
                'id' => $tableRow["id"],
                'fullName' => $tableRow["fullname"],
                'birthDate' => $tableRow["birthdate"],
                'gender' => $tableRow["gender"],
                'address' => $tableRow["address"],
                'email' => $tableRow["email"],
                'phoneNumber' => $tableRow["phonenumber"]
            ];
        }
        echo json_encode($responseData);
    }
}

/*
 {
  "fullName": "string",
  "birthDate": "2022-12-02T11:05:28.893Z",
  "gender": "Male",
  "address": "string",
  "phoneNumber": "string"
}
 */
function putData($requestData): void
{
    global $link;

    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("401", "User is unauthorized");
    } else {
        if (getProfileDataValidationResult($requestData)) {
            $email = getEmailFromToken($token);
            $fullName = $requestData->body->fullName;
            $address = $requestData->body->address;
            $birthdate = $requestData->body->birthDate;
            $gender = $requestData->body->gender;
            $phoneNumber = $requestData->body->phoneNumber;

            pg_query($link, "update users set
                                        fullname = '$fullName',
                                        address = '$address',
                                        birthdate = '$birthdate',
                                        gender = '$gender',
                                        phonenumber = '$phoneNumber'
                                    where email = '$email'");

            setHttpStatus("200", "Profile changes saved");
        }
    }
}