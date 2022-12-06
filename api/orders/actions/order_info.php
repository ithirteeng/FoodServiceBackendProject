<?php
function checkRequestMethods($method): bool
{
    if ($method == "GET") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function getData($requestData, $orderId): void
{

}