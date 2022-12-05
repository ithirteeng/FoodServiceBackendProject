<?php

function getRequestData($method): stdClass
{
    $data = new stdClass();

    if ($method != "GET") {
        $data->body = (object)json_decode(file_get_contents("php://input"), true);
    }
    $data->parameters = getQueryParams();
    return $data;
}

function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

function getQueryParams(): array
{
    $url = $_SERVER['QUERY_STRING'];
    $data = explode("&", $url);
    $result = [];
    $count = 0;
    foreach ($data as $param) {
        $tempArray = explode("=", $param);
        if ($tempArray[0] != 'q') {
            if ($tempArray[0] == "category") {
                $result[$tempArray[0]][$count] = $tempArray[1];
                $count++;
            } else {
                $result[$tempArray[0]] = $tempArray[1];
            }
        }

    }
    return $result;
}
