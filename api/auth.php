<?php
/**
 * API: Authentication Endpoints
 * ResQ Emergency Response System
 */

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        handlePost($action, $conn);
        break;
    case 'GET':
        handleGet($action, $conn);
        break;
    default:
        jsonResponse(false, 'Method not allowed', [], 405);
}

function handlePost($action, $conn) {
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'register':
            registerUser($input, $conn);
            break;
        case 'login':
            loginUser($input, $conn);
            break;
        case 'logout':
            logoutUser($conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function handleGet($action, $conn) {
    switch ($action) {
        case 'user':
            getCurrentUser($conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function registerUser($input, $conn) {
    if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
        jsonResponse(false, 'Name, email and password are required', [], 422);
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Email already registered', [], 422);
    }

    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, user_type) VALUES (?, ?, ?, ?, 'user')");
    $stmt->execute([$input['name'], $input['email'], $hashedPassword, $input['phone'] ?? null]);
    $userId = $conn->lastInsertId();

    // Create profile
    $stmt = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
    $stmt->execute([$userId]);

    // Create medical info
    $stmt = $conn->prepare("INSERT INTO medical_info (user_id) VALUES (?)");
    $stmt->execute([$userId]);

    $token = base64_encode($userId . ':' . time());

    jsonResponse(true, 'User registered successfully', [
        'user' => ['id' => $userId, 'name' => $input['name'], 'email' => $input['email']],
        'token' => $token
    ], 201);
}

function loginUser($input, $conn) {
    if (empty($input['email']) || empty($input['password'])) {
        jsonResponse(false, 'Email and password are required', [], 422);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($input['password'], $user['password'])) {
        jsonResponse(false, 'Invalid credentials', [], 401);
    }

    if (!$user['is_active']) {
        jsonResponse(false, 'Account is deactivated', [], 403);
    }

    $token = base64_encode($user['id'] . ':' . time());

    // Log the login
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address) VALUES (?, 'user.login', 'User logged in', ?)");
    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'] ?? 'unknown']);

    jsonResponse(true, 'Login successful', [
        'user' => $user,
        'token' => $token
    ]);
}

function logoutUser($conn) {
    jsonResponse(true, 'Logged out successfully');
}

function getCurrentUser($conn) {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(false, 'Unauthorized', [], 401);
    }

    // Get profile and medical info
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();

    $stmt = $conn->prepare("SELECT * FROM medical_info WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $medicalInfo = $stmt->fetch();

    $user['profile'] = $profile;
    $user['medical_info'] = $medicalInfo;

    jsonResponse(true, '', $user);
}