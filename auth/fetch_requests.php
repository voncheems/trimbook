<?php
// fetch_requests.php (Soft delete - counts deleted as resolved)
session_start();
require_once '../includes/dbconfig.php';

// Set JSON header
header('Content-Type: application/json');

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query based on filters - EXCLUDE soft-deleted from display
$query = "SELECT id, email, phone, status, submitted_at, resolved_at, ip_address, user_agent
          FROM password_reset_requests 
          WHERE deleted_at IS NULL";

$types = "";
$params = [];

// Add search filter
if (!empty($search)) {
    $query .= " AND (email LIKE ? OR phone LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $types .= "ss";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Add status filter
if ($status !== 'all') {
    $query .= " AND status = ?";
    $types .= "s";
    $params[] = $status;
}

// Order by most recent first
$query .= " ORDER BY submitted_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database prepare failed: ' . $conn->error
    ]);
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}
$stmt->close();

// Get statistics - Include deleted items in resolved count
$stats = [
    'pending' => 0,
    'resolved' => 0,
    'total' => 0
];

// Count pending (not deleted)
$pendingQuery = "SELECT COUNT(*) as count 
                 FROM password_reset_requests 
                 WHERE status = 'pending' AND deleted_at IS NULL";
$pendingResult = $conn->query($pendingQuery);
if ($pendingResult) {
    $row = $pendingResult->fetch_assoc();
    $stats['pending'] = (int)$row['count'];
}

// Count resolved (including deleted ones)
$resolvedQuery = "SELECT COUNT(*) as count 
                  FROM password_reset_requests 
                  WHERE status = 'resolved'";
$resolvedResult = $conn->query($resolvedQuery);
if ($resolvedResult) {
    $row = $resolvedResult->fetch_assoc();
    $stats['resolved'] = (int)$row['count'];
}

// Total active (only non-deleted)
$totalQuery = "SELECT COUNT(*) as count 
               FROM password_reset_requests 
               WHERE deleted_at IS NULL";
$totalResult = $conn->query($totalQuery);
if ($totalResult) {
    $row = $totalResult->fetch_assoc();
    $stats['total'] = (int)$row['count'];
}

$conn->close();

// Return response
echo json_encode([
    'success' => true,
    'requests' => $requests,
    'stats' => $stats
]);
?>
