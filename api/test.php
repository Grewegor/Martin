<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status' => 'OK',
    'message' => 'PHP работает правильно',
    'php_version' => phpversion(),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
