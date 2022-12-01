<?php

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function postData($requestData): void
{
    echo "postLogout";
    global $link;
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    $email = $requestData->body->email;

    if (checkTokenExistence($token)) {
        $newToken = createToken($email);
        echo json_encode(["token" => $newToken]);
        pg_query($link, "insert into token_blacklist(value) values ('$token')");
    } else {
        setHttpStatus("401", "User is not authorized");
    }
}