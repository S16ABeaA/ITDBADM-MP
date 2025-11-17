<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_bs'])){
  // Retrieve form data
  $shoeId = $_POST['shoeID'] ?? null;
  $shoeName = $_POST['shoeName'] ?? '';
  $shoeBrand = $_POST['shoeBrand'] ?? '';
  $shoeSize = $_POST['shoeSize'] ?? '';
  $shoeGender = $_POST['shoeGender'] ?? '';
  $shoePrice = $_POST['shoePrice'] ?? '';
  $shoeStock = $_POST['shoeStock'] ?? '';
  $shoeImage = $_POST['shoeImage'] ?? '';
  // Validate required fields
  if(!$shoeId || empty($shoeImage) || empty($shoeName) || empty($shoeBrand) || empty($shoeSize) || empty($shoeGender) || empty($shoePrice) || empty($shoeStock)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling shoe.'
    ]);
    exit;
  }

  // Verify that the selected brand exists
  // Resolve Brand: accept numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($shoeBrand)) {
    $candidateId = intval($shoeBrand);
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
    $brandStmt->bind_param('s', $shoeBrand);
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

  // Update bowling shoe (escape and cast)
  $shoeNameEsc = $conn->real_escape_string($shoeName);
  $shoeSizeEsc = $conn->real_escape_string($shoeSize);
  $shoeGenderEsc = $conn->real_escape_string($shoeGender);
  $shoeImageEsc = $conn->real_escape_string($shoeImage);
  $shoePriceNum = is_numeric($shoePrice) ? floatval($shoePrice) : $conn->real_escape_string($shoePrice);
  $shoeStockNum = is_numeric($shoeStock) ? intval($shoeStock) : $conn->real_escape_string($shoeStock);
  $brandParam = intval($resolvedBrandId);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $updatebsquery = "UPDATE bowlingshoes SET Name='$shoeNameEsc', size='$shoeSizeEsc', sex='$shoeGenderEsc' WHERE ProductID='$shoeId'";
  $updatebsresult = $conn->query($updatebsquery);

  $updateproductquery = "UPDATE product SET Price=$shoePriceNum, Quantity=$shoeStockNum, ImageID='$shoeImageEsc', BrandID=$brandParam WHERE ProductID='$shoeId' AND BranchID=$branchID";
  $updateproductresult = $conn->query($updateproductquery);
  if($updatebsresult && $updateproductresult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling shoe has been updated successfully.',
      'productId' => $shoeId,
      'data' => [
        'productId' => $shoeId,
        'shoeName' => $shoeName,
        'shoeBrand' => $shoeBrand,
        'shoeSize' => $shoeSize,
        'shoeGender' => $shoeGender,
        'shoePrice' => '₱' . number_format($shoePrice, 2),
        'shoeStock' => $shoeStock,
        'shoeImage' => $shoeImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update bowling shoe: ' . $conn->error
    ]);
  }
  exit;
}
?>