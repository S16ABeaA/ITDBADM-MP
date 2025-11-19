<?php
require_once '../dependencies/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

$branchId = $_GET['branch_id'] ?? $_SESSION['staff_branch_id'] ?? null;
$branchFilterSQL = '';
if ($branchId) {
  $branchFilterSQL = ' AND o.BranchID = ' . intval($branchId);
}

$sql = "
SELECT 
    QUARTER(o.DatePurchased) AS quarter,
    YEAR(o.DatePurchased) AS year,
    SUM(o.Total) AS revenue
FROM orders o
WHERE o.DatePurchased IS NOT NULL" . $branchFilterSQL . "
GROUP BY year, quarter
ORDER BY year, quarter;
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "label" => "Q" . $row["quarter"] . " " . $row["year"],
        "revenue" => (float)$row["revenue"]
    ];
}

echo json_encode($data);
?>
