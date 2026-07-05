<?php
/**
 * API: Emergency Request Endpoints
 * ResQ Emergency Response System
 */

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'POST':
        handlePost($action, $conn);
        break;
    case 'GET':
        handleGet($action, $id, $conn);
        break;
    case 'PUT':
        handlePut($action, $id, $conn);
        break;
    default:
        jsonResponse(false, 'Method not allowed', [], 405);
}

function handlePost($action, $conn) {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(false, 'Unauthorized', [], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'request':
            createRequest($input, $user, $conn);
            break;
        case 'sos':
            createSOS($input, $user, $conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function handleGet($action, $id, $conn) {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(false, 'Unauthorized', [], 401);
    }

    switch ($action) {
        case 'types':
            getTypes($conn);
            break;
        case 'requests':
            getRequests($user, $conn);
            break;
        case 'request':
            getRequest($id, $user, $conn);
            break;
        case 'track':
            trackRequest($id, $user, $conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function handlePut($action, $id, $conn) {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(false, 'Unauthorized', [], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'status':
            updateStatus($id, $input, $user, $conn);
            break;
        case 'location':
            updateLocation($id, $input, $user, $conn);
            break;
        default:
            jsonResponse(false, 'Invalid action', [], 404);
    }
}

function getTypes($conn) {
    $stmt = $conn->query("SELECT * FROM emergency_types WHERE is_active = 1 ORDER BY name");
    $types = $stmt->fetchAll();
    jsonResponse(true, '', $types);
}

function createRequest($input, $user, $conn) {
    if (empty($input['emergency_type_id']) || empty($input['description']) ||
        !isset($input['latitude']) || !isset($input['longitude'])) {
        jsonResponse(false, 'Missing required fields', [], 422);
    }

    $incidentNumber = 'INC-' . strtoupper(substr(md5(time()), 0, 8));

    $stmt = $conn->prepare("INSERT INTO emergency_requests
        (requester_id, emergency_type_id, incident_number, description, latitude, longitude, address, is_sos)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user['id'],
        $input['emergency_type_id'],
        $incidentNumber,
        $input['description'],
        $input['latitude'],
        $input['longitude'],
        $input['address'] ?? null,
        $input['is_sos'] ?? false
    ]);

    $requestId = $conn->lastInsertId();

    // Create history
    $stmt = $conn->prepare("INSERT INTO incident_history (emergency_request_id, status, notes, created_by) VALUES (?, 'pending', 'Emergency request created', ?)");
    $stmt->execute([$requestId, $user['id']]);

    // Log
    logAction($user['id'], 'emergency.request.created', "Created request $incidentNumber", $conn);

    $stmt = $conn->prepare("SELECT er.*, et.name as type_name, et.icon, et.color FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        WHERE er.id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();

    jsonResponse(true, 'Emergency request submitted', $request, 201);
}

function createSOS($input, $user, $conn) {
    if (!isset($input['latitude']) || !isset($input['longitude'])) {
        jsonResponse(false, 'Location is required', [], 422);
    }

    // Get default emergency type (medical)
    $stmt = $conn->query("SELECT id FROM emergency_types WHERE code = 'medical' LIMIT 1");
    $type = $stmt->fetch();
    $typeId = $type['id'] ?? 1;

    $incidentNumber = 'SOS-' . strtoupper(substr(md5(time()), 0, 6));

    $stmt = $conn->prepare("INSERT INTO emergency_requests
        (requester_id, emergency_type_id, incident_number, description, latitude, longitude, address, is_sos, status)
        VALUES (?, ?, ?, 'SOS Alert - Immediate assistance required', ?, ?, ?, true, 'pending')");
    $stmt->execute([
        $user['id'],
        $typeId,
        $incidentNumber,
        $input['latitude'],
        $input['longitude'],
        $input['address'] ?? null
    ]);

    $requestId = $conn->lastInsertId();

    // Create history
    $stmt = $conn->prepare("INSERT INTO incident_history (emergency_request_id, status, notes, created_by) VALUES (?, 'pending', 'SOS Alert triggered', ?)");
    $stmt->execute([$requestId, $user['id']]);

    logAction($user['id'], 'emergency.sos.created', "Created SOS $incidentNumber", $conn);

    $stmt = $conn->prepare("SELECT er.*, et.name as type_name, et.icon FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        WHERE er.id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();

    jsonResponse(true, 'SOS Alert sent! Help is on the way.', $request, 201);
}

function getRequests($user, $conn) {
    $stmt = $conn->prepare("SELECT er.*, et.name as type_name, et.icon, et.color
        FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        WHERE er.requester_id = ?
        ORDER BY er.created_at DESC
        LIMIT 50");
    $stmt->execute([$user['id']]);
    $requests = $stmt->fetchAll();
    jsonResponse(true, '', $requests);
}

function getRequest($id, $user, $conn) {
    $stmt = $conn->prepare("SELECT er.*, et.name as type_name, et.icon, et.color, u.name as requester_name, u.phone as requester_phone
        FROM emergency_requests er
        LEFT JOIN emergency_types et ON er.emergency_type_id = et.id
        LEFT JOIN users u ON er.requester_id = u.id
        WHERE er.id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch();

    if (!$request) {
        jsonResponse(false, 'Request not found', [], 404);
    }

    if ($request['requester_id'] != $user['id'] && !isAdmin($user)) {
        jsonResponse(false, 'Unauthorized', [], 403);
    }

    // Get responders
    $stmt = $conn->prepare("SELECT ir.*, r.badge_number, r.status as responder_status, u.name as responder_name
        FROM incident_responders ir
        LEFT JOIN responders r ON ir.responder_id = r.id
        LEFT JOIN users u ON r.user_id = u.id
        WHERE ir.emergency_request_id = ?");
    $stmt->execute([$id]);
    $responders = $stmt->fetchAll();

    $request['responders'] = $responders;

    jsonResponse(true, '', $request);
}

function trackRequest($id, $user, $conn) {
    getRequest($id, $user, $conn);
}

function updateStatus($id, $input, $user, $conn) {
    if (empty($input['status'])) {
        jsonResponse(false, 'Status is required', [], 422);
    }

    $stmt = $conn->prepare("SELECT * FROM emergency_requests WHERE id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch();

    if (!$request) {
        jsonResponse(false, 'Request not found', [], 404);
    }

    $data = ['status' => $input['status']];
    if ($input['status'] === 'completed') {
        $data['completed_at'] = date('Y-m-d H:i:s');
    }

    $stmt = $conn->prepare("UPDATE emergency_requests SET status = ? WHERE id = ?");
    $stmt->execute([$input['status'], $id]);

    // Create history
    $stmt = $conn->prepare("INSERT INTO incident_history (emergency_request_id, status, notes, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $input['status'], $input['notes'] ?? 'Status updated', $user['id']]);

    jsonResponse(true, 'Status updated');
}

function updateLocation($id, $input, $user, $conn) {
    if (!isset($input['latitude']) || !isset($input['longitude'])) {
        jsonResponse(false, 'Location is required', [], 422);
    }

    $stmt = $conn->prepare("UPDATE emergency_requests SET latitude = ?, longitude = ?, last_location_update = NOW() WHERE id = ? AND requester_id = ?");
    $stmt->execute([$input['latitude'], $input['longitude'], $id, $user['id']]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Request not found', [], 404);
    }

    jsonResponse(true, 'Location updated');
}

function logAction($userId, $action, $description, $conn) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
}