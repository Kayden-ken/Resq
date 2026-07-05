<?php
/**
 * Database Connection - ResQ Emergency Response System
 * Uses MySQL from Laragon when available.
 */

$db_host = 'localhost';
$db_name = 'resq';
$db_user = 'root';
$db_pass = '';

$conn = null;

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conn = null;
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($success, $message = '', $data = [], $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($token)) {
            return null;
        }

        global $conn;
        if (!$conn) {
            return null;
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)substr($token, 0, 10)]);
        return $stmt->fetch();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin($user) {
        return $user && in_array($user['user_type'], ['admin', 'dispatcher']);
    }
}