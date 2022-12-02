<?php

require_once "database_helper.php";

function createToken($email): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload = ['email' => $email];
    $secretKey = "pchel";

    $currentTime = new DateTime();
    $payload['nbf'] = $currentTime->getTimestamp();
    $payload['exp'] = $currentTime->getTimestamp() + 3600;
    $payload['iat'] = $currentTime->getTimestamp();
    $payload['iss'] = "http://localhost/";
    $payload['aud'] = "http://localhost/";


    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $secretKey, true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}


function getTokenPayload($token): array
{
    $tokenParts = explode(".", $token);
    //echo $tokenParts[1] . PHP_EOL;
    return json_decode(base64_decode($tokenParts[1]), true);
}

function getTokenHeader($token): string
{
    $tokenParts = explode(".", $token);
    //echo $tokenParts[0] . PHP_EOL;
    return $tokenParts[0];
}

function getTokenSignature($token): string
{
    $tokenParts = explode(".", $token);
    //echo $tokenParts[2] . PHP_EOL;
    return $tokenParts[2];
}

function checkIfTokenIsExpired($token): bool
{
    $payload = getTokenPayload($token);
    return $payload['exp'] < (new DateTime())->getTimestamp();
}

function checkTokenExistence($token): bool
{

    $payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode(getTokenPayload($token))));
    $header = getTokenHeader($token);

    $signature = hash_hmac('sha256', $header . '.' . $payload, "pchel", true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    //echo "sign  " . $base64UrlSignature . PHP_EOL;

    return $base64UrlSignature == getTokenSignature($token);
}

function addTokenToBlackList($token): void
{
    global $link;
    if (countTokensInBlackList() > 50) {
        pg_query($link, "delete from token_blacklist");
    }
    if (!checkIfTokenInBlackList($token)) {
        pg_query($link, "insert into token_blacklist (value) values ('$token')");
    }
}

function countTokensInBlackList(): int
{
    global $link;
    $result = pg_fetch_assoc(pg_query($link, "select count(*) from token_blacklist"));
    return $result['count'];
}

function checkIfTokenInBlackList($token): bool
{
    global $link;
    if (pg_fetch_assoc(
        pg_query($link, "select value from token_blacklist where value = '$token'")
    )) {
        return true;
    } else {
        return false;
    }
}

function getEmailFromToken($token): string
{
    $payload = getTokenPayload($token);
    return $payload['email'];
}