<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['update_order'])){
  // Retrieve form data
  $orderId = $_POST['orderId'] ?? null;
  $status= $_POST['status'] ?? '';
  $dateCompleted = $_POST['dateCompleted'] ?? null;
  
  // Validate required fields
  if(!$orderId || empty($status)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill the status field.'
    ]);
    exit;
  }
  
  // Validate status is one of allowed values
  $allowedStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
  if(!in_array($status, $allowedStatuses)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Invalid status value.'
    ]);
    exit;
  }
  
  // Update order status in database
  $updateQuery = "CALL UpdateOrderStatus('$orderId', '$status')";
  // Add date completed if provided
  if($status === 'Completed' && !empty($dateCompleted)){
    $updateQuery .= ", DateCompleted='$dateCompleted'";
  } else if($status !== 'Completed'){
    // Clear date completed if not Completed
    $updateQuery .= ", DateCompleted=NULL";
  }
  $updateQuery .= " WHERE OrderID='$orderId'";
  
  $updateResult = $conn->query($updateQuery);
  
  if($updateResult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => "Order $orderId status updated to $status successfully.",
      'orderId' => $orderId,
      'data' => [
        'orderId' => $orderId,
        'status' => $status,
        'dateCompleted' => $dateCompleted
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update order: ' . $conn->error
    ]);
  }
  exit;
}

// If update_order flag not set, return error
http_response_code(400);
echo json_encode([
  'success' => false,
  'message' => 'Invalid request.'
]);
?>
