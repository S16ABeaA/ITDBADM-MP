<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_bb'])){
  // Retrieve form data
  $ballId = $_POST['ballID'] ?? null;
  $ballName = $_POST['ballName'] ?? '';
  $ballBrand = $_POST['ballBrand'] ?? '';
  $ballPrice = $_POST['ballPrice'] ?? '';
  $ballStock = $_POST['ballStock'] ?? '';
  $ballWeight = $_POST['ballWeight'] ?? '';
  $ballType = $_POST['ballType'] ?? '';
  $ballQuality = $_POST['ballQuality'] ?? '';
  $coreName = $_POST['coreName'] ?? '';
  $coreType = $_POST['coreType'] ?? '';
  $rgValue = $_POST['rgValue'] ?? '';
  $diffValue = $_POST['diffValue'] ?? '';
  $intDiffValue = $_POST['intDiffValue'] ?? '';
  $coverstockName = $_POST['coverstockName'] ?? '';
  $coverstockType = $_POST['coverstockType'] ?? '';
  $ballImage = $_POST['ballImage'] ?? '';
  
  // Validate required fields
  if(!$ballId || empty($ballImage) || empty($ballName) || empty($ballBrand) || empty($ballPrice) || empty($ballStock) || empty($ballWeight) || empty($ballType) || empty($ballQuality) || empty($coreName) || empty($coreType) || empty($rgValue) || empty($diffValue) || empty($intDiffValue) || empty($coverstockName) || empty($coverstockType)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to update the bowling ball.'
    ]);
    exit;
  }

  // Resolve Brand: accept numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($ballBrand)) {
    $candidateId = intval($ballBrand);
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
    $brandStmt->bind_param('s', $ballBrand);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Selected brand does not exist.']); $brandStmt->close(); exit; }
    $brandStmt->bind_result($foundId); $brandStmt->fetch(); $resolvedBrandId = intval($foundId); $brandStmt->close();
  }

  /* Handle image upload (optional for updates)
  $bb_image = $_POST['ballImage_existing'] ?? '';
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
  } else if(empty($bb_image)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Image file is required.'
    ]);
    exit;
  }*/

  // Update bowling ball (escape and cast)
  $ballQualityEsc = $conn->real_escape_string($ballQuality);
  $ballNameEsc = $conn->real_escape_string($ballName);
  $ballTypeEsc = $conn->real_escape_string($ballType);
  $rgValueNum = is_numeric($rgValue) ? floatval($rgValue) : $conn->real_escape_string($rgValue);
  $diffValueNum = is_numeric($diffValue) ? floatval($diffValue) : $conn->real_escape_string($diffValue);
  $intDiffValueNum = is_numeric($intDiffValue) ? floatval($intDiffValue) : $conn->real_escape_string($intDiffValue);
  $ballWeightNum = is_numeric($ballWeight) ? floatval($ballWeight) : $conn->real_escape_string($ballWeight);
  $coreTypeEsc = $conn->real_escape_string($coreType);
  $coreNameEsc = $conn->real_escape_string($coreName);
  $coverstockNameEsc = $conn->real_escape_string($coverstockName);
  $coverstockTypeEsc = $conn->real_escape_string($coverstockType);
  $ballImageEsc = $conn->real_escape_string($ballImage);
  $ballPriceNum = is_numeric($ballPrice) ? floatval($ballPrice) : $conn->real_escape_string($ballPrice);
  $ballStockNum = is_numeric($ballStock) ? intval($ballStock) : $conn->real_escape_string($ballStock);
  $brandParam = intval($resolvedBrandId);
  // Resolve branch and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid or missing branch']); exit; }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID); $bst->execute(); $bst->store_result(); if ($bst->num_rows===0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; } $bst->close();

  $updatebbquery = "UPDATE bowlingball SET Quality='$ballQualityEsc', Name='$ballNameEsc', Type='$ballTypeEsc', RG=$rgValueNum, DIFF=$diffValueNum, INTDIFF=$intDiffValueNum, weight=$ballWeightNum, CoreType='$coreTypeEsc', CoreName='$coreNameEsc', Coverstock='$coverstockNameEsc', CoverstockType='$coverstockTypeEsc' WHERE ProductID='$ballId'";
  $updatebbresult = $conn->query($updatebbquery);

  $updateproductquery = "UPDATE product SET Price=$ballPriceNum, Quantity=$ballStockNum, ImageID='$ballImageEsc', BrandID=$brandParam WHERE ProductID='$ballId' AND BranchID=$branchID";
  $updateproductresult = $conn->query($updateproductquery);
  
  if($updatebbresult && $updateproductresult){
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling ball has been updated successfully.',
      'productId' => $ballId,
      'data' => [
        'productId' => $ballId,
        'ballName' => $ballName,
        'ballBrand' => $ballBrand,
        'ballType' => $ballType,
        'ballQuality' => $ballQuality,
        'ballWeight' => $ballWeight . ' lbs',
        'coreName' => $coreName,
        'coreType' => $coreType,
        'rgValue' => $rgValue,
        'diffValue' => $diffValue,
        'intDiffValue' => $intDiffValue,
        'coverstockName' => $coverstockName,
        'coverstockType' => $coverstockType,
        'ballPrice' => '₱' . number_format($ballPrice, 2),
        'ballStock' => $ballStock,
        'ballImage' => $ballImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to update bowling ball: ' . $conn->error
    ]);
  }
  exit;
}
?>