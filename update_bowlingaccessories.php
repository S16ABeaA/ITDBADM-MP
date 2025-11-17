<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_ba'])){
  // Retrieve form data
  $accessoryId = $_POST['accessoryID'] ?? null;
  $accessoryName = $_POST['accessoryName'] ?? '';
  $accessoryBrand = $_POST['accessoryBrand'] ?? '';
  $accessoryType = $_POST['accessoryType'] ?? '';
  $handedness = $_POST['handedness'] ?? '';
  $accessoryPrice = $_POST['accessoryPrice'] ?? '';
  $accessoryStock = $_POST['accessoryStock'] ?? '';
  $accessoryImage = $_POST['accessoryImage'] ?? '';
  // Validate required fields
  if(!$accessoryId || empty($accessoryImage) || empty($accessoryName) || empty($accessoryBrand) || empty($handedness) || empty($accessoryType) || empty($accessoryPrice) || empty($accessoryStock)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to update the bowling accessory.'
    ]);
    exit;
  }

  // Resolve Brand: accept numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($accessoryBrand)) {
    $candidateId = intval($accessoryBrand);
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
    $brandStmt->bind_param('s', $accessoryBrand);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Selected brand does not exist.']); $brandStmt->close(); exit; }
    $brandStmt->bind_result($foundId); $brandStmt->fetch(); $resolvedBrandId = intval($foundId); $brandStmt->close();
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

  // Update bowling accessory (escape and cast)
  $accessoryNameEsc = $conn->real_escape_string($accessoryName);
  $accessoryTypeEsc = $conn->real_escape_string($accessoryType);
  $handednessEsc = $conn->real_escape_string($handedness);
  $accessoryImageEsc = $conn->real_escape_string($accessoryImage);
  $accessoryPriceNum = is_numeric($accessoryPrice) ? floatval($accessoryPrice) : $conn->real_escape_string($accessoryPrice);
  $accessoryStockNum = is_numeric($accessoryStock) ? intval($accessoryStock) : $conn->real_escape_string($accessoryStock);
  $brandParam = intval($resolvedBrandId);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $updatebgquery = "UPDATE bowlingaccessories SET Name='$accessoryNameEsc', Type='$accessoryTypeEsc', Handedness='$handednessEsc' WHERE ProductID='$accessoryId'";
  $updatebgresult = $conn->query($updatebgquery);

  $updateproductquery = "UPDATE product SET Price=$accessoryPriceNum, Quantity=$accessoryStockNum, ImageID='$accessoryImageEsc', BrandID=$brandParam WHERE ProductID='$accessoryId' AND BranchID=$branchID";
  $updateproductresult = $conn->query($updateproductquery);
  
  if($updatebgresult && $updateproductresult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling accessory has been updated successfully.',
      'productId' => $accessoryId,
      'data' => [
        'productId' => $accessoryId,
        'accessoryName' => $accessoryName,
        'accessoryBrand' => $accessoryBrand,
        'accessoryType' => $accessoryType,
        'handedness' => $handedness,
        'accessoryPrice' => '₱' . number_format($accessoryPrice, 2),
        'accessoryStock' => $accessoryStock,
        'accessoryImage' => $accessoryImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update bowling accessory: ' . $conn->error
    ]);
  }
  exit;
}
?>