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

$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Укажите имя пользователя и пароль']);
    exit;
}

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
$file = $dataDir . DIRECTORY_SEPARATOR . 'users.json';

if (!file_exists($file)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Неверное имя пользователя или пароль']);
    exit;
}

$content = file_get_contents($file);
$users = json_decode($content, true);

if (!is_array($users)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Неверное имя пользователя или пароль']);
    exit;
}

$user = null;
foreach ($users as $u) {
    if ($u['username'] === $username && $u['password'] === $password) {
        $user = $u;
        break;
    }
}

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Неверное имя пользователя или пароль']);
    exit;
}

$token = bin2hex(random_bytes(32));
$tokensDir = $dataDir . DIRECTORY_SEPARATOR . 'tokens';
if (!is_dir($tokensDir)) {
    @mkdir($tokensDir, 0755, true);
}

$tokenFile = $tokensDir . DIRECTORY_SEPARATOR . hash('sha256', $token) . '.txt';
$tokenData = [
    'token' => $token,
    'userId' => $user['id'],
    'username' => $user['username'],
    'createdAt' => time(),
    'expiresAt' => time() + (7 * 24 * 60 * 60)
];

file_put_contents($tokenFile, json_encode($tokenData));

echo json_encode([
    'success' => true,
    'token' => $token,
    'username' => $user['username'],
    'message' => 'Вы успешно вошли'
]);
?>
