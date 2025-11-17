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
  $bb_image = $_POST['ballImage'] ?? '';
  
  // Validate required fields
  if(empty($bb_image) || empty($ballName) || empty($ballBrand) || empty($ballPrice) || empty($ballStock) || empty($ballWeight) || empty($ballType) || empty($ballQuality) || empty($coreName) || empty($coreType) || empty($rgValue) || empty($diffValue) || empty($intDiffValue) || empty($coverstockName) || empty($coverstockType)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling ball.'
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
  }

  // Verify that the selected brand exists
  if (!is_numeric($ballBrand)) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Invalid brand selected.'
    ]);
    exit;
  }*/

  // Resolve Brand: accept either numeric BrandID or a brand Name
  $resolvedBrandId = null;
  if (is_numeric($ballBrand)) {
    $candidateId = intval($ballBrand);
    $brandStmt = $conn->prepare("SELECT BrandID FROM brand WHERE BrandID = ?");
    if (!$brandStmt) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Database error (brand check).']);
      exit;
    }
    $brandStmt->bind_param('i', $candidateId);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => 'Selected brand does not exist.'
      ]);
      $brandStmt->close();
      exit;
    }
    $brandStmt->bind_result($foundId);
    $brandStmt->fetch();
    $resolvedBrandId = intval($foundId);
    $brandStmt->close();
  } else {
    $brandStmt = $conn->prepare("SELECT BrandID FROM brand WHERE Name = ?");
    if (!$brandStmt) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Database error (brand check).']);
      exit;
    }
    $brandStmt->bind_param('s', $ballBrand);
    $brandStmt->execute();
    $brandStmt->store_result();
    if ($brandStmt->num_rows === 0) {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => 'Selected brand does not exist.'
      ]);
      $brandStmt->close();
      exit;
    }
    $brandStmt->bind_result($foundId);
    $brandStmt->fetch();
    $resolvedBrandId = intval($foundId);
    $brandStmt->close();
  }

  // Check if bowling ball already exists
  $bbchecker = "SELECT * FROM bowlingball WHERE Quality = '$ballQuality' AND Name = '$ballName' AND Type = '$ballType' AND RG = '$rgValue' AND DIFF = '$diffValue' AND INTDIFF = '$intDiffValue' AND weight = '$ballWeight' AND CoreName = '$coreName' AND CoreType = '$coreType' AND Coverstock = '$coverstockName' AND CoverstockType = '$coverstockType'";
  $bbresult = $conn->query($bbchecker);
  
  if($bbresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This bowling ball already exists.'
    ]);
    exit;
  }

  // Insert new bowling ball
  // Resolve current branch (from POST or session) and validate
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $branchID = $_POST['branchID'] ?? $_SESSION['staff_branch_id'] ?? null;
  if (!$branchID || !is_numeric($branchID)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing branch']);
    exit;
  }
  $branchID = intval($branchID);
  $bst = $conn->prepare("SELECT BranchID FROM branches WHERE BranchID = ? LIMIT 1");
  if (!$bst) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Database error (branch check).']); exit; }
  $bst->bind_param('i', $branchID);
  $bst->execute();
  $bst->store_result();
  if ($bst->num_rows === 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Branch not found']); $bst->close(); exit; }
  $bst->close();

  $brandParam = intval($resolvedBrandId);

  // Sanitize inputs for the CALL string to avoid SQL errors and injection
  $ballNameEsc = $conn->real_escape_string($ballName);
  $bb_imageEsc = $conn->real_escape_string($bb_image);
  $ballQualityEsc = $conn->real_escape_string($ballQuality);
  $ballTypeEsc = $conn->real_escape_string($ballType);
  $ballWeightNum = is_numeric($ballWeight) ? floatval($ballWeight) : $conn->real_escape_string($ballWeight);
  $coreTypeEsc = $conn->real_escape_string($coreType);
  $coreNameEsc = $conn->real_escape_string($coreName);
  $coverstockNameEsc = $conn->real_escape_string($coverstockName);
  $coverstockTypeEsc = $conn->real_escape_string($coverstockType);
  $rgValueNum = is_numeric($rgValue) ? floatval($rgValue) : $conn->real_escape_string($rgValue);
  $diffValueNum = is_numeric($diffValue) ? floatval($diffValue) : $conn->real_escape_string($diffValue);
  $intDiffValueNum = is_numeric($intDiffValue) ? floatval($intDiffValue) : $conn->real_escape_string($intDiffValue);
  $ballPriceNum = is_numeric($ballPrice) ? floatval($ballPrice) : $conn->real_escape_string($ballPrice);
  $ballStockNum = is_numeric($ballStock) ? intval($ballStock) : $conn->real_escape_string($ballStock);

  $insertbbquery = "CALL AddBowlingBall($branchID, $brandParam, '$ballNameEsc', $ballPriceNum, '$bb_imageEsc', '$ballQualityEsc', '$ballTypeEsc', '$ballWeightNum', '$coreTypeEsc', '$coreNameEsc', '$coverstockNameEsc', '$coverstockTypeEsc', $rgValueNum, $diffValueNum, $intDiffValueNum, $ballStockNum)";
  $insertbbresult = $conn->query($insertbbquery);
  
  if($insertbbresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling ball has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
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
        'ballImage' => $bb_image
      ,  'branchID' => $branchID
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert bowling ball: ' . $conn->error
    ]);
  }
  exit;
}
?>