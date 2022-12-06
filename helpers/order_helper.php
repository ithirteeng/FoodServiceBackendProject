<?php

function checkOrderIdExisting($orderId): bool
{
    global $link;
    $regex = "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";
    if (preg_match($regex, $orderId)) {
        if (pg_fetch_assoc(
            pg_query($link, "select id from \"order\" where id = '$orderId'")
    )) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
