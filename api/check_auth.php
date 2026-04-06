<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    echo json_encode(['authenticated' => false]);
    exit;
}

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
$tokensDir = $dataDir . DIRECTORY_SEPARATOR . 'tokens';
$tokenFile = $tokensDir . DIRECTORY_SEPARATOR . hash('sha256', $token) . '.txt';

if (!file_exists($tokenFile)) {
    echo json_encode(['authenticated' => false]);
    exit;
}

$content = file_get_contents($tokenFile);
$tokenData = json_decode($content, true);

if (!$tokenData || time() > $tokenData['expiresAt']) {
    @unlink($tokenFile);
    echo json_encode(['authenticated' => false]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'username' => $tokenData['username'],
    'userId' => $tokenData['userId']
]);
?>
