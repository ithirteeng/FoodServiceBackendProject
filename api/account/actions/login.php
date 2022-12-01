<?php

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
    $requestEmail = $requestData->body->email;
    $requestPassword = $requestData->body->password;

    // email check
    if (pg_fetch_assoc(
        pg_query($link, "select email from users where email = '$requestEmail'")
    )) {
        // password check
        $newPassword = hash('sha1', $requestPassword);
        if (pg_fetch_assoc(
            pg_query($link, "select password from users where password = '$newPassword'")
        )) {
            $newToken = createToken($requestEmail);
            if (checkIfTokenInBlackList($newToken)) {
                pg_query($link, "delete from token_blacklist where value = '$newToken'");
            }
            echo json_encode(["token" => $newToken]);
        } else {
            setHttpStatus("400", "password is incorrect");
        }
    } else {
        setHttpStatus("401", "User doesn't exist");
    }

}