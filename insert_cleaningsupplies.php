<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_cs'])){
  // Retrieve form data
  $cleaningId = $_POST['cleaningID'] ?? null;
  $supplyName = $_POST['supplyName'] ?? '';
  $supplyBrand = $_POST['supplyBrand'] ?? '';
  $supplyType = $_POST['supplyType'] ?? '';
  $supplyPrice = $_POST['supplyPrice'] ?? '';
  $supplyStock = $_POST['supplyStock'] ?? '';
  $supplyImage = $_POST['supplyImage'] ?? '';
  // Validate required fields
  if(empty($supplyImage) || empty($supplyName) || empty($supplyBrand) || empty($supplyType) || empty($supplyPrice) || empty($supplyStock)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the cleaning supply.'
    ]);
    exit;
  }

  /* Handle image upload
  $bb_image = '';
  if(isset($_FILES['ballImage']) && $_FILES['ballImage']['error'] === UPLOAD_ERR_OK){
    $xt = pathinfo($_FILES['ballImage']['name'], PATHINFO_EXTENSION);
    $allowedtypes = array("jpg", "jpeg", "png", "gif", "webp");
    $tempname = $_FILES['ballImage']['tmp_name'];
    
    if(!in_array($xt, $allowedtypes)){
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Allowed types: jpg, jpeg, png, gif, webp'
      ]);
      exit;
    }
    
    $bb_image = $_FILES['ballImage']['name'];
    $target = __DIR__ . '/images/' . $bb_image;
    
    if(!move_uploaded_file($tempname, $target)){
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'message' => 'Failed to upload image.'
      ]);
      exit;
    }
  } else {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Image file is required.'
    ]);
    exit;
  }*/

  // Check if bowling accessory already exists
  // Resolve Brand: accept numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($supplyBrand)) {
    $candidateId = intval($supplyBrand);
    $brandStmt = $conn->prepare("SELECT BrandID FROM brand WHERE BrandID = ?");
    if (!$brandStmt) { http_response_code(500); echo json_encode(['success' => false, 'message' => 'Database error (brand check).']); exit; }
    $brandStmt->bind_param('i', $candidateId);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Selected brand does not exist.']); $brandStmt->close(); exit; }
    $brandStmt->bind_result($foundId); $brandStmt->fetch(); $resolvedBrandId = intval($foundId); $brandStmt->close();
  } else {
    $brandStmt = $conn->prepare("SELECT BrandID FROM brand WHERE Name = ?");
    if (!$brandStmt) { http_response_code(500); echo json_encode(['success' => false, 'message' => 'Database error (brand check).']); exit; }
    $brandStmt->bind_param('s', $supplyBrand);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Selected brand does not exist.']); $brandStmt->close(); exit; }
    $brandStmt->bind_result($foundId); $brandStmt->fetch(); $resolvedBrandId = intval($foundId); $brandStmt->close();
  }

  $supplyNameEsc = $conn->real_escape_string($supplyName);
  $supplyTypeEsc = $conn->real_escape_string($supplyType);
  $cschecker = "SELECT * FROM cleaningsupplies WHERE Name = '$supplyNameEsc' AND Type = '$supplyTypeEsc'";
  $csresult = $conn->query($cschecker);
  
  if($csresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This cleaning supply already exists.'
    ]);
    exit;
  }

  // Insert new cleaning supply (escape and cast)
  $brandParam = intval($resolvedBrandId);
  $supplyNameEsc = $conn->real_escape_string($supplyName);
  $supplyImageEsc = $conn->real_escape_string($supplyImage);
  $supplyTypeEsc = $conn->real_escape_string($supplyType);
  $supplyPriceNum = is_numeric($supplyPrice) ? floatval($supplyPrice) : $conn->real_escape_string($supplyPrice);
  $supplyStockNum = is_numeric($supplyStock) ? intval($supplyStock) : $conn->real_escape_string($supplyStock);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $insertcsquery = "CALL AddCleaningSupplies($branchID, $brandParam, '$supplyNameEsc', $supplyPriceNum, '$supplyImageEsc', '$supplyTypeEsc', $supplyStockNum)";
  $insertcsresult = $conn->query($insertcsquery);
  
  if($insertcsresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Cleaning supply has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
        'supplyName' => $supplyName,
        'supplyBrand' => $supplyBrand,
        'supplyPrice' => '₱' . number_format($supplyPrice, 2),
        'supplyType' => $supplyType,
        'supplyStock' => $supplyStock,
        'supplyImage' => $supplyImage,
        'branchID' => $branchID
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert cleaning supply: ' . $conn->error
    ]);
  }
  exit;
}
?>