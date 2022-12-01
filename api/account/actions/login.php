<?php

require_once "helpers/jwt_helper.php";

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

/**
 * @throws Exception
 */
function postLoginData($requestData): void
{
    $token = createToken("shjkf@email");
    echo $token . PHP_EOL;

}