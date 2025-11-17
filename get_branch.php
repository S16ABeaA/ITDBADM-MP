<?php
session_start();
header('Content-Type: application/json');

$branchId = $_SESSION['staff_branch_id'] ?? null;
$branchName = $_SESSION['staff_branch_name'] ?? null;

if ($branchId) {
  echo json_encode(['success' => true, 'branchId' => $branchId, 'branchName' => $branchName]);
} else {
  echo json_encode(['success' => false, 'branchId' => null, 'branchName' => null]);
}
exit;
?>
