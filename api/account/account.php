<?php
function route($method, $urlList, $requestData): void
{
    $filename = realpath(dirname(__FILE__)) . "/actions/" . $urlList[2] . ".php";
    if (count($urlList) == 3 and file_exists($filename)) {
        require_once $filename;
        checkRequestMethods($method);
        switch ($method) {
            case "POST":
                postData($requestData);
                break;
            case "GET":
                getData();
                break;
            case "PUT":
                putData($requestData);
                break;
            default:
                break;
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }

}
