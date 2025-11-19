<?php
require_once '../dependencies/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

$branchId = $_GET['branch_id'] ?? $_SESSION['staff_branch_id'] ?? null;
$branchFilterSQL = '';
if ($branchId) {
  $branchFilterSQL = ' WHERE BranchID = ' . intval($branchId);
}

$query = "
    SELECT 
        CASE Type
            WHEN 'ball' THEN 'Bowling Balls'
            WHEN 'shoes' THEN 'Shoes'
            WHEN 'bag' THEN 'Bags'
            WHEN 'accessories' THEN 'Accessories'
            WHEN 'supplies' THEN 'Cleaning'
        END AS Category,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM product" . $branchFilterSQL . "), 2) AS percentage
    FROM product" . $branchFilterSQL . "
    GROUP BY Type;
";

$result = $conn->query($query);

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['Category'];
    $data[] = $row['percentage'];
}

// Important: Print JSON for your JS script to use
echo json_encode([
    "labels" => $labels,
    "data" => $data
]);
?>
