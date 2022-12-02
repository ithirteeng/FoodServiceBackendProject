<?php

require_once "helpers/jwt_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/http_status_helper.php";

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
        $profileData =pg_query($link, "select * from users where email = '$email'");
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

function putData($requestData): void
{
    echo "putProfile";
}