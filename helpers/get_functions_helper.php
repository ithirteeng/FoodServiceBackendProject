<?php

function getRequestData($method): stdClass
{
    $data = new stdClass();

    if ($method != "GET") {
        $data->body = json_decode(file_get_contents("php://input"), true);
    }

    $data->parameters = [];
    $getData = $_GET;
    foreach ($getData as $key => $value) {
        if ($key != 'q') {
            $data->parameters[$key] = $value;
        }
    }
    return $data;
}

function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}
