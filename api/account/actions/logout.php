<?php

require_once "helpers/jwt_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/http_status_helper.php";

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function postData($requestData): void
{
    global $link;
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("403", "Token in blacklist");
    } else {
        if (checkTokenExistence($token)) {
            addTokenToBlackList($token);
            setHttpStatus("200", "Logged out");
        } else {
            setHttpStatus("401", "Token not specified or not valid");
        }
    }

}