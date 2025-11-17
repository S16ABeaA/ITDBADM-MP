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
  if(!$cleaningId || empty($supplyImage) || empty($supplyName) || empty($supplyBrand) || empty($supplyType) || empty($supplyPrice) || empty($supplyStock)){
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

  // Update cleaning supply
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

  // Update cleaning supply (escape and cast)
  $supplyNameEsc = $conn->real_escape_string($supplyName);
  $supplyTypeEsc = $conn->real_escape_string($supplyType);
  $supplyImageEsc = $conn->real_escape_string($supplyImage);
  $supplyPriceNum = is_numeric($supplyPrice) ? floatval($supplyPrice) : $conn->real_escape_string($supplyPrice);
  $supplyStockNum = is_numeric($supplyStock) ? intval($supplyStock) : $conn->real_escape_string($supplyStock);
  $brandParam = intval($resolvedBrandId);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $updatecsquery = "UPDATE cleaningsupplies SET Name='$supplyNameEsc', Type='$supplyTypeEsc' WHERE ProductID='$cleaningId'";
  $updatecsresult = $conn->query($updatecsquery);

  $updateproductquery = "UPDATE product SET Price=$supplyPriceNum, Quantity=$supplyStockNum, ImageID='$supplyImageEsc', BrandID=$brandParam WHERE ProductID='$cleaningId' AND BranchID=$branchID";
  $updateproductresult = $conn->query($updateproductquery);
  
  if($updatecsresult && $updateproductresult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Cleaning supply has been updated successfully.',
      'productId' => $cleaningId,
      'data' => [
        'productId' => $cleaningId,
        'supplyName' => $supplyName,
        'supplyBrand' => $supplyBrand,
        'supplyType' => $supplyType,
        'supplyPrice' => '₱' . number_format($supplyPrice, 2),
        'supplyStock' => $supplyStock,
        'supplyImage' => $supplyImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update cleaning supply: ' . $conn->error
    ]);
  }
  exit;
}
?>