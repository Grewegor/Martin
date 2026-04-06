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
$email = isset($data['email']) ? trim($data['email']) : '';

if (empty($username) || empty($password) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Все поля обязательны']);
    exit;
}

if (strlen($username) < 3 || strlen($username) > 20) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Имя пользователя должно содержать от 3 до 20 символов']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректный email']);
    exit;
}

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
$file = $dataDir . DIRECTORY_SEPARATOR . 'users.json';

if (!is_dir($dataDir)) {
    if (!@mkdir($dataDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Ошибка сервера']);
        exit;
    }
}

if (!file_exists($file)) {
    $users = [];
} else {
    $content = file_get_contents($file);
    $users = json_decode($content, true);
    if (!is_array($users)) {
        $users = [];
    }
}

foreach ($users as $u) {
    if ($u['username'] === $username) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Пользователь с таким именем уже существует']);
        exit;
    }
    if ($u['email'] === $email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email уже зарегистрирован']);
        exit;
    }
}

$maxId = 0;
foreach ($users as $u) {
    if (isset($u['id']) && $u['id'] > $maxId) {
        $maxId = $u['id'];
    }
}

$newUser = [
    'id' => $maxId + 1,
    'username' => $username,
    'password' => $password,
    'email' => $email,
    'createdAt' => date('Y-m-d')
];

$users[] = $newUser;

if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка записи в файл']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Регистрация успешна']);
?>
