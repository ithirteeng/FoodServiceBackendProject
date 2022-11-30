<?php

function getRequestData($method)
{
    $data = new stdClass();

    if ($method != "GET") {
        $data->body = json_decode(file_get_contents("php://input"), true);
    }

    $data->parameters = [];
    $getData = $_GET;
    foreach ($getData as $key => $value) {
        if ($key != 'q') {
            $data->parameters[$key] = $value;
        }
    }
    return $data;
}

function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
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
// если есть чт-то -> то ставим, иначе пустота
$url = isset($_GET['q']) ? $_GET['q'] : "";
// удаляем мусор справа (в нащем случае - /)
$url = rtrim($url, '/');
// получаем массив, через /
$urlList = explode('/', $url);


$router = $urlList[1];
$requestData = getRequestData(getRequestMethod());

$filename = realpath(dirname(__FILE__)) . "/" . $urlList[0] . "/" . $router . ".php";

echo $filename . PHP_EOL;

if (file_exists($filename)) {
    include_once $filename;
    route(getRequestMethod(), $urlList, $requestData);
} else {
    echo "SORRY, guys: 404";
}









