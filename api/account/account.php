<?php
function route($method, $urlList, $requestData): void
{
    global $link;
    if (count($urlList) == 3) {
        switch ($method) {

        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }


}
