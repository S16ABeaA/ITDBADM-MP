<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

$sql = "SELECT BranchID, Name FROM branches ORDER BY Name";
$result = $conn->query($sql);

if (!$result) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  exit;
}

$branches = [];
while ($row = $result->fetch_assoc()) {
  $branches[] = $row;
}

echo json_encode(['success' => true, 'branches' => $branches]);
$conn->close();
exit;
?>
