<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_bg'])){
  // Retrieve form data
  $bagId = $_POST['bagID'] ?? null;
  $bagName = $_POST['bagName'] ?? '';
  $bagBrand = $_POST['bagBrand'] ?? '';
  $bagColor = $_POST['bagColor'] ?? '';
  $bagSize = $_POST['bagSize'] ?? '';
  $bagType = $_POST['bagType'] ?? '';
  $bagPrice = $_POST['bagPrice'] ?? '';
  $bagStock = $_POST['bagStock'] ?? '';
  $bagImage = $_POST['bagImage'] ?? '';
  // Validate required fields
  if(!$bagId || empty($bagImage) || empty($bagName) || empty($bagBrand) || empty($bagColor) || empty($bagType) || empty($bagPrice) || empty($bagStock)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling bag.'
    ]);
    exit;
  }

  // Resolve Brand: accept numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($bagBrand)) {
    $candidateId = intval($bagBrand);
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
    $brandStmt->bind_param('s', $bagBrand);
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


  // Update bowling bag (escape and cast values)
  $bagNameEsc = $conn->real_escape_string($bagName);
  $bagTypeEsc = $conn->real_escape_string($bagType);
  $bagSizeEsc = $conn->real_escape_string($bagSize);
  $bagColorEsc = $conn->real_escape_string($bagColor);
  $bagPriceNum = is_numeric($bagPrice) ? floatval($bagPrice) : $conn->real_escape_string($bagPrice);
  $bagStockNum = is_numeric($bagStock) ? intval($bagStock) : $conn->real_escape_string($bagStock);
  $bagImageEsc = $conn->real_escape_string($bagImage);
  $brandParam = intval($resolvedBrandId);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $updatebgquery = "UPDATE bowlingbag SET Name='$bagNameEsc', Type='$bagTypeEsc', Size='$bagSizeEsc', color='$bagColorEsc' WHERE ProductID='$bagId'";
  $updatebgresult = $conn->query($updatebgquery);

  $updateproductquery = "UPDATE product SET Price=$bagPriceNum, Quantity=$bagStockNum, ImageID='$bagImageEsc', BrandID=$brandParam WHERE ProductID='$bagId' AND BranchID=$branchID";
  $updateproductresult = $conn->query($updateproductquery);
  if($updatebgresult && $updateproductresult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling bag has been updated successfully.',
      'productId' => $bagId,
      'data' => [
        'productId' => $bagId,
        'bagName' => $bagName,
        'bagBrand' => $bagBrand,
        'bagSize' => $bagSize,
        'bagColor' => $bagColor,
        'bagPrice' => '₱' . number_format($bagPrice, 2),
        'bagStock' => $bagStock,
        'bagImage' => $bagImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update bowling bag: ' . $conn->error
    ]);
  }
  exit;
}
?>