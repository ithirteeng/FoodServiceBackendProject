<?php

function checkRequestMethods($method): bool
{
    if ($method != "GET") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return true;
    } else {
        return false;
    }
}

function getData($requestData, $id): void
{

}
