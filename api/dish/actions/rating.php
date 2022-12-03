<?php
function checkRequestMethods($method): bool
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return true;
    } else {
        return false;
    }
}

function postData($requestData, $id): void
{

}
