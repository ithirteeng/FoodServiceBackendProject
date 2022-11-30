<?php

function checkRequestMethods($method): void
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
    }
}

function postLoginData($requestData): void
{
    echo "postLogin";
}