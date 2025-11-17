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
  if(empty($accessoryImage) || empty($accessoryName) || empty($accessoryBrand) || empty($handedness) || empty($accessoryType) || empty($accessoryPrice) || empty($accessoryStock)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling accessory.'
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

  $bachecker = "SELECT * FROM bowlingaccessories WHERE Name = '$accessoryName' AND Type = '$accessoryType' AND Handedness = '$handedness'";
  $baresult = $conn->query($bachecker);
  
  if($baresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This bowling accessory already exists.'
    ]);
    exit;
  }

  // Insert new bowling accessory (escape and cast)
  $brandParam = intval($resolvedBrandId);
  $accessoryNameEsc = $conn->real_escape_string($accessoryName);
  $accessoryImageEsc = $conn->real_escape_string($accessoryImage);
  $accessoryTypeEsc = $conn->real_escape_string($accessoryType);
  $handednessEsc = $conn->real_escape_string($handedness);
  $accessoryPriceNum = is_numeric($accessoryPrice) ? floatval($accessoryPrice) : $conn->real_escape_string($accessoryPrice);
  $accessoryStockNum = is_numeric($accessoryStock) ? intval($accessoryStock) : $conn->real_escape_string($accessoryStock);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $insertbaquery = "CALL AddBowlingAccessories($branchID, $brandParam, '$accessoryNameEsc', $accessoryPriceNum, '$accessoryImageEsc', '$accessoryTypeEsc', '$handednessEsc', $accessoryStockNum)";
  $insertbaresult = $conn->query($insertbaquery);
  
  if($insertbaresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling accessory has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
        'accessoryName' => $accessoryName,
        'accessoryBrand' => $accessoryBrand,
        'accessoryPrice' => '₱' . number_format($accessoryPrice, 2),
        'accessoryType' => $accessoryType,
        'handedness' => $handedness,
        'accessoryStock' => $accessoryStock,
        'accessoryImage' => $accessoryImage,
        'branchID' => $branchID
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert bowling accessory: ' . $conn->error
    ]);
  }
  exit;
}
?>