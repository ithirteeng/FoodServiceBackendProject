<?php

function setHttpStatus($codeStatus = "200", $responseMessage = null): void
{
    switch ($codeStatus) {
        default:
        case "200":
            $textStatus = "HTTP/1.0 200 OK";
            break;
        case "400":
            $textStatus = "HTTP/1.0 400 Bad Request";
            break;
        case "401":
            $textStatus = "HTTP/1.0 401 Unauthorized";
            break;
        case "403":
            $textStatus = "HTTP/1.0 403 Forbidden";
            break;
        case "404":
            $textStatus = "HTTP/1.0 404 Not Found";
            break;
        case "405":
            $textStatus = "HTTP/1.0 405 Method Not Allowed";
            break;
        case "409":
            $textStatus = "HTTP/1.0 409 Conflict";
            break;
        case "415":
            $textStatus = "HTTP/1.0 415 Unsupported Media Type";
            break;
        case "500":
            $textStatus = "HTTP/1.0 500 Internal Server Error";
            break;
    }
    header($textStatus);
    if (!is_null($responseMessage) && !is_array($responseMessage)) {
        echo json_encode(
            [
                'status' => $codeStatus,
                'message' => $responseMessage
            ]
        );
    } else if (!empty($responseMessage)) {
        echo json_encode($responseMessage);
    }
}