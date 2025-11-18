<?php
require_once '../dependencies/config.php';
header('Content-Type: application/json');

$monthlyData = [];

// Loop through each month (1 to 12)
for ($i = 1; $i <= 12; $i++) {
    $sql = "SELECT SUM(Total) as total_per_month FROM orders WHERE MONTH(DatePurchased) = ? AND YEAR(DatePurchased) = YEAR(CURDATE());";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $i);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // If no sales for the month, use 0
        $monthlyData[] = $row['total_per_month'] ? (float)$row['total_per_month'] : 0;
    } else {
        $monthlyData[] = 0;
    }
    
    $stmt->close();
}

$conn->close();

// Return the array directly instead of wrapping in 'data'
echo json_encode($monthlyData);
?>