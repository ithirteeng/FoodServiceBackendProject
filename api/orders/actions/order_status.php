<?php

function checkRequestMethods($method): bool {
    if ($method == "POST") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function postData($requestData, $orderId): void {

}