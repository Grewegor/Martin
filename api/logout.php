<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$token = isset($data['token']) ? trim($data['token']) : '';

if (empty($token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Токен не предоставлен']);
    exit;
}

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
$tokensDir = $dataDir . DIRECTORY_SEPARATOR . 'tokens';
$tokenFile = $tokensDir . DIRECTORY_SEPARATOR . hash('sha256', $token) . '.txt';

if (file_exists($tokenFile)) {
    @unlink($tokenFile);
}

echo json_encode(['success' => true, 'message' => 'Вы вышли из системы']);
?>
