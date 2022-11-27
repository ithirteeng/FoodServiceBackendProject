<?php

function getData($method)
{
    if ($method == "GET") {
        return $_GET;
    } else {
        return json_decode(file_get_contents("php://input"), true);
    }
}

header('Content-type: application/json');

$link = pg_connect("host=localhost port=5432 dbname=food_service_db user=hits password=hits");

if (!$link) {
    echo "Ошибка: невозможно установить подключение с БД" . PHP_EOL;
    echo "Код ошибки: " . pg_last_error($link) . PHP_EOL;
    echo "Текст ошибки: " . pg_last_error($link) . PHP_EOL;
}

$res = pg_query($link, "SELECT * FROM test_table order by id");

$message = array(
    'users' => array()
);

if (!$res) {
    echo "Не удалось выполнить запрос: (" . pg_last_error($link) . ") ";
} else {
    while ($row = pg_fetch_assoc($res)) {
        $message['users'][] = [
            'id' => $row["id"],
            'login' => $row["login"],
            'name' => $row["name"]
        ];
    }
}

//echo json_encode(getData("GET"));
echo file_get_contents("php://input");





