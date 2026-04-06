<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);

$method = $_SERVER['REQUEST_METHOD'];

// Получаем путь к файлу с данными
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
$file = $dataDir . DIRECTORY_SEPARATOR . 'albums.json';

if ($method === 'GET') {
    // Добавление нового альбома
    $year = isset($_GET['year']) ? trim($_GET['year']) : '';
    $title = isset($_GET['title']) ? trim($_GET['title']) : '';

    if ($year === '' || $title === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Укажите год и название альбома']);
        exit;
    }

    if (!is_dir($dataDir)) {
        if (!@mkdir($dataDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Не удалось создать папку data']);
            exit;
        }
    }

    if (!file_exists($file)) {
        $albums = [];
    } else {
        $content = file_get_contents($file);
        $albums = json_decode($content, true);
        if (!is_array($albums)) {
            $albums = [];
        }
    }

    $albums[] = ['year' => $year, 'title' => $title];

    if (file_put_contents($file, json_encode($albums, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Ошибка записи в файл']);
        exit;
    }

    echo json_encode(['success' => true]);

} elseif ($method === 'POST') {
    // Сохранение всего списка альбомов (используется при удалении)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['albums']) || !is_array($data['albums'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Некорректные данные']);
        exit;
    }

    if (!is_dir($dataDir)) {
        if (!@mkdir($dataDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Не удалось создать папку data']);
            exit;
        }
    }

    $albums = $data['albums'];

    if (file_put_contents($file, json_encode($albums, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Ошибка записи в файл']);
        exit;
    }

    echo json_encode(['success' => true]);

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}
