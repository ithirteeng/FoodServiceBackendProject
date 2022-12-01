<?php

function checkRequestMethods($method): void
{
    if ($method != "GET" && $method != "PUT") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function getData($requestData): void
{
    echo "getProfile";
}

function putData($requestData): void
{
    echo "putProfile";
}