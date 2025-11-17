<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$branch = $_POST['branch'] ?? null;
if (!$branch) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'No branch provided']);
  exit;
}

// Try to resolve by ID or Name
if (is_numeric($branch)) {
  $stmt = $conn->prepare("SELECT BranchID, Name FROM branches WHERE BranchID = ? LIMIT 1");
  $bid = intval($branch);
  $stmt->bind_param('i', $bid);
} else {
  $stmt = $conn->prepare("SELECT BranchID, Name FROM branches WHERE Name = ? LIMIT 1");
  $stmt->bind_param('s', $branch);
}

if (!$stmt) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error (prepare)']);
  exit;
}

$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
  http_response_code(404);
  echo json_encode(['success' => false, 'message' => 'Branch not found']);
  $stmt->close();
  exit;
}

$row = $res->fetch_assoc();
$_SESSION['staff_branch_id'] = intval($row['BranchID']);
$_SESSION['staff_branch_name'] = $row['Name'];

echo json_encode(['success' => true, 'branchId' => $_SESSION['staff_branch_id'], 'branchName' => $_SESSION['staff_branch_name']]);
$stmt->close();
exit;
?>
