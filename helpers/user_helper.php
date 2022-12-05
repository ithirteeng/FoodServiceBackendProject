<?php
require_once "database_helper.php";
require_once "http_status_helper.php";
require_once "jwt_helper.php";

function checkUserToken($token): bool {
    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
        return false;
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("401", "User is unauthorized"); //error code?
        return false;
    } else {
        return true;
    }
}