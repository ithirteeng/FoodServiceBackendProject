<?php
require_once "database_helper.php";
require_once "http_status_helper.php";
require_once "jwt_helper.php";

function getUserIdByToken($token): string
{
    global $link;
    $email = getEmailFromToken($token);
    $userData = pg_query($link, "select id from users where email = '$email'");
    return pg_fetch_assoc($userData)['id'];
}

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

function checkUserTokenForDish($token): bool {
    if (checkIfTokenIsExpired($token)) {
        addTokenToBlackList($token);
        return false;
    } else if (checkIfTokenInBlackList($token)) {
        return false;
    } else {
        return true;
    }
}