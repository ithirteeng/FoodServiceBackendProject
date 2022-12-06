<?php
require_once "helpers/validation_helper.php";
require_once "helpers/order_helper.php";
require_once "helpers/jwt_helper.php";
require_once "helpers/user_helper.php";

function checkRequestMethods($method): bool
{
    if ($method == "POST" || $method == "GET") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function getData($requestData): void
{

}

function postData($requestData): void
{

}
