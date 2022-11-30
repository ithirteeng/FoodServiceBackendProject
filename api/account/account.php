<?php
function route($method, $urlList, $requestData): void
{
    $filename = realpath(dirname(__FILE__)) . "/actions/" . $urlList[2] . ".php";
    if (count($urlList) == 3 and file_exists($filename)) {
        require_once $filename;
        checkRequestMethods($method);
        switch ($method) {
            case "POST":
                if ($urlList[2] == "login") {
                    postLoginData($requestData);
                } else if ($urlList[2] == "logout") {
                    postLogoutData($requestData);
                } else {
                    postRegisterData($requestData);
                }
                break;
            case "GET":
                getProfileData($requestData);
                break;
            case "PUT":
                putProfileData($requestData);
                break;
            default:
                break;
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }

}
