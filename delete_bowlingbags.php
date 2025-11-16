<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['delete_bowlingbag'])){
    // Retrieve and validate the product ID and optional branch ID
    $productId = $_POST['productID'] ?? null;
    $branchId = $_POST['branchID'] ?? null;
    
    if(!$productId || !is_numeric($productId)){
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID provided.'
        ]);
        exit;
    }
    
    if($branchId && !is_numeric($branchId)){
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid branch ID provided.'
        ]);
        exit;
    }
    
    // Start a transaction
    $conn->begin_transaction();
    
    try {
        // Delete from bowlingbag table
        $deleteStmt = $conn->prepare("DELETE FROM bowlingbag WHERE ProductID = ?");
        if(!$deleteStmt){
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $deleteStmt->bind_param("i", $productId);
        
        if(!$deleteStmt->execute()){
            throw new Exception("Execute failed: " . $deleteStmt->error);
        }
        
        // Check if any rows were deleted
        if($deleteStmt->affected_rows === 0){
            $conn->rollback();
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Bowling bag not found.'
            ]);
            $deleteStmt->close();
            exit;
        }
        
        // Delete from product table - either from specific branch or all branches
        if($branchId){
            // Delete only from specific branch
            $deleteProductStmt = $conn->prepare("DELETE FROM product WHERE ProductID = ? AND BranchID = ?");
            if(!$deleteProductStmt){
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $deleteProductStmt->bind_param("ii", $productId, $branchId);
        } else {
            // Delete from all branches
            $deleteProductStmt = $conn->prepare("DELETE FROM product WHERE ProductID = ?");
            if(!$deleteProductStmt){
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $deleteProductStmt->bind_param("i", $productId);
        }
        
        if(!$deleteProductStmt->execute()){
            throw new Exception("Execute failed: " . $deleteProductStmt->error);
        }
        
        $deleteStmt->close();
        $deleteProductStmt->close();
        
        // Commit the transaction
        $conn->commit();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Bowling bag deleted successfully.',
            'productId' => intval($productId)
        ]);
        
    } catch(Exception $e){
        $conn->rollback();
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while deleting the bowling bag: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
}
?>
