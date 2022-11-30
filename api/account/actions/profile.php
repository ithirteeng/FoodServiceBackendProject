<?php

function checkRequestMethods($method): void
{
    if ($method != "GET" && $method != "PUT") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function getProfileData($requestData): void
{
    echo "getProfile";
}

function putProfileData($requestData): void
{
    echo "putProfile";
}