<?php
/**
 * API: Admin Endpoints
 * ResQ Emergency Response System
 */

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        handleGet($action, $id, $conn);
        break;
    case 'PUT':
    case 'POST':
        handlePostPut($action, $id, $conn);
        break;
    default:
        jsonResponse(false, 'Method not allowed', [], 405);
}

function handleGet($action, $id, $conn) {
    $user = getCurrentUser();
    if (!$user || !isAdmin($user)) {
        jsonResponse(false, 'Admin access required', [], 403);
    }

    switch ($action) {
        case 'dashboard':
            getDashboard($conn);
            break;
        case 'requests':
            getRequests($conn);
            break;
        case 'users':
            getUsers($conn);
            break;
        case 'responders':
            getResponders($conn);
            break;
        case 'reports':
            getReports($conn);
            break;
        case 'audit':
            getAuditLogs($conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function handlePostPut($action, $id, $conn) {
    $user = getCurrentUser();
    if (!$user || !isAdmin($user)) {
        jsonResponse(false, 'Admin access required', [], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'request_update':
            updateRequest($id, $input, $user, $conn);
            break;
        case 'assign_responder':
            assignResponder($id, $input, $user, $conn);
            break;
        case 'user_update':
            updateUser($id, $input, $user, $conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function getDashboard($conn) {
    // Stats
    $stats = [
        'total_requests' => $conn->query("SELECT COUNT(*) FROM emergency_requests")->fetchColumn(),
        'pending_requests' => $conn->query("SELECT COUNT(*) FROM emergency_requests WHERE status = 'pending'")->fetchColumn(),
        'active_requests' => $conn->query("SELECT COUNT(*) FROM emergency_requests WHERE status IN ('accepted', 'responding', 'arrived')")->fetchColumn(),
        'completed_today' => $conn->query("SELECT COUNT(*) FROM emergency_requests WHERE DATE(completed_at) = CURDATE()")->fetchColumn(),
        'total_users' => $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'user'")->fetchColumn(),
        'total_responders' => $conn->query("SELECT COUNT(*) FROM responders")->fetchColumn(),
        'available_responders' => $conn->query("SELECT COUNT(*) FROM responders WHERE status = 'available'")->fetchColumn(),
    ];

    // Recent requests
    $stmt = $conn->query("SELECT er.*, et.name as type_name, et.icon, u.name as requester_name
        FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        LEFT JOIN users u ON er.requester_id = u.id
        ORDER BY er.created_at DESC
        LIMIT 10");
    $recentRequests = $stmt->fetchAll();

    // Type distribution
    $stmt = $conn->query("SELECT emergency_type_id, count(*) as count FROM emergency_requests GROUP BY emergency_type_id");
    $typeDistribution = $stmt->fetchAll();

    jsonResponse(true, '', [
        'stats' => $stats,
        'recent_requests' => $recentRequests,
        'type_distribution' => $typeDistribution
    ]);
}

function getRequests($conn) {
    $status = $_GET['status'] ?? '';
    $type = $_GET['type'] ?? '';

    $sql = "SELECT er.*, et.name as type_name, et.icon, et.color, u.name as requester_name
        FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        LEFT JOIN users u ON er.requester_id = u.id
        WHERE 1=1";

    if ($status) {
        $sql .= " AND er.status = '$status'";
    }
    if ($type) {
        $sql .= " AND er.emergency_type_id = '$type'";
    }

    $sql .= " ORDER BY er.created_at DESC LIMIT 50";

    $stmt = $conn->query($sql);
    $requests = $stmt->fetchAll();

    jsonResponse(true, '', $requests);
}

function getUsers($conn) {
    $search = $_GET['search'] ?? '';
    $userType = $_GET['user_type'] ?? '';

    $sql = "SELECT id, name, email, phone, user_type, is_active, created_at FROM users WHERE 1=1";

    if ($search) {
        $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
    }
    if ($userType) {
        $sql .= " AND user_type = '$userType'";
    }

    $sql .= " ORDER BY created_at DESC LIMIT 50";

    $stmt = $conn->query($sql);
    $users = $stmt->fetchAll();

    jsonResponse(true, '', $users);
}

function getResponders($conn) {
    $sql = "SELECT r.*, u.name as user_name, u.email as user_email, ea.name as agency_name
        FROM responders r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN emergency_agencies ea ON r.agency_id = ea.id
        ORDER BY r.created_at DESC LIMIT 50";

    $stmt = $conn->query($sql);
    $responders = $stmt->fetchAll();

    jsonResponse(true, '', $responders);
}

function getReports($conn) {
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');

    // Total incidents
    $totalRequests = $conn->query("SELECT COUNT(*) FROM emergency_requests WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'")->fetchColumn();

    // Incidents by type
    $stmt = $conn->query("SELECT et.name, COUNT(er.id) as count
        FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        WHERE DATE(er.created_at) BETWEEN '$startDate' AND '$endDate'
        GROUP BY er.emergency_type_id");
    $incidentsByType = $stmt->fetchAll();

    // Daily incidents
    $stmt = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count
        FROM emergency_requests
        WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'
        GROUP BY DATE(created_at)
        ORDER BY date");
    $dailyIncidents = $stmt->fetchAll();

    // Status distribution
    $stmt = $conn->query("SELECT status, COUNT(*) as count
        FROM emergency_requests
        WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'
        GROUP BY status");
    $statusDistribution = $stmt->fetchAll();

    jsonResponse(true, '', [
        'total_requests' => $totalRequests,
        'incidents_by_type' => $incidentsByType,
        'daily_incidents' => $dailyIncidents,
        'status_distribution' => $statusDistribution
    ]);
}

function getAuditLogs($conn) {
    $limit = $_GET['limit'] ?? 50;

    $stmt = $conn->query("SELECT al.*, u.name as user_name
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT $limit");
    $logs = $stmt->fetchAll();

    jsonResponse(true, '', $logs);
}

function updateRequest($id, $input, $user, $conn) {
    if (empty($input['status'])) {
        jsonResponse(false, 'Status is required', [], 422);
    }

    $data = ['status' => $input['status']];
    if ($input['status'] === 'completed') {
        $data['completed_at'] = date('Y-m-d H:i:s');
    }

    $stmt = $conn->prepare("UPDATE emergency_requests SET status = ? WHERE id = ?");
    $stmt->execute([$input['status'], $id]);

    // Log
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address) VALUES (?, 'admin.request_updated', ?, ?)");
    $stmt->execute([$user['id'], "Updated request status to {$input['status']}", $_SERVER['REMOTE_ADDR']]);

    jsonResponse(true, 'Request updated');
}

function assignResponder($id, $input, $conn) {
    if (empty($input['responder_id'])) {
        jsonResponse(false, 'Responder ID is required', [], 422);
    }

    // Check if already assigned
    $stmt = $conn->prepare("SELECT id FROM incident_responders WHERE emergency_request_id = ? AND responder_id = ?");
    $stmt->execute([$id, $input['responder_id']]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Responder already assigned', [], 400);
    }

    // Create assignment
    $stmt = $conn->prepare("INSERT INTO incident_responders (emergency_request_id, responder_id, status, assigned_at) VALUES (?, ?, 'assigned', NOW())");
    $stmt->execute([$id, $input['responder_id']]);

    // Update request status
    $stmt = $conn->prepare("UPDATE emergency_requests SET status = 'accepted' WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);

    jsonResponse(true, 'Responder assigned');
}

function updateUser($id, $input, $user, $conn) {
    if (!empty($input['is_active'])) {
        $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$input['is_active'], $id]);
    }

    jsonResponse(true, 'User updated');
}