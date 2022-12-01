<?php

/**
 * @throws Exception
 */
function createToken($email): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload = ['email' => $email];
    $secretKey = bin2hex(random_bytes(32));

    $currentTime = new DateTime();
    $payload['nbf'] = $currentTime->getTimestamp();
    $payload['exp'] = $currentTime->getTimestamp() + 60;
    $payload['iat'] = $currentTime->getTimestamp();
    $payload['iss'] = "http://localhost/";
    $payload['aud'] = "http://localhost/";


    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, base64_encode($secretKey), true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function checkIsTokenValid($token): bool {
    $tokenParts = explode(".", $token);
    return $tokenParts[0] == "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9";
}

function getTokenPayload($token) {
    $tokenParts = explode(".", $token);
    return $tokenParts[1];
}

function checkIfTokenIsExpired($token): bool {
    $payload = getTokenPayload($token);
    return $payload['exp'] < (new DateTime())->getTimestamp();
}