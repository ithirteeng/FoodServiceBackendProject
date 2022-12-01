<?php

function createToken($email): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload = ['email' => $email];
    $secretKey = "pchel";

    $currentTime = new DateTime();
    $payload['nbf'] = $currentTime->getTimestamp();
    $payload['exp'] = $currentTime->getTimestamp() + 30;
    $payload['iat'] = $currentTime->getTimestamp();
    $payload['iss'] = "http://localhost/";
    $payload['aud'] = "http://localhost/";


    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $secretKey, true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}


function getTokenPayload($token): string
{
    $tokenParts = explode(".", $token);
    //echo $tokenParts[1] . PHP_EOL;
    return $tokenParts[1];
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
    $payload = getTokenPayload($token);
    $header = getTokenHeader($token);

    $signature = hash_hmac('sha256', $header . '.' . $payload, "pchel", true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    //echo "sign  " . $base64UrlSignature . PHP_EOL;

    return $base64UrlSignature == getTokenSignature($token);
}

function checkIfTokenInBlackList($token): bool {
    global $link;
    if (pg_fetch_assoc(
        pg_query($link, "select value from token_blacklist where value = '$token'")
    )) {
        return false;
    } else {
        return true;
    }
}