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
  $ballDescription = $_POST['ballDescription'] ?? '';
  
  // Validate required fields
  if(empty($ballName) || empty($ballBrand) || empty($ballPrice) || empty($ballStock) || empty($ballWeight) || empty($ballType) || empty($ballQuality) || empty($coreName) || empty($coreType) || empty($rgValue) || empty($diffValue) || empty($intDiffValue) || empty($coverstockName) || empty($coverstockType)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling ball.'
    ]);
    exit;
  }

  // Handle image upload
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
  $insertbbquery = "CALL AddBowlingBall('1', '$ballBrand', '$ballName', '$ballPrice', '$bb_image', '$ballQuality', '$ballType', '$ballWeight', '$coreType', '$coreName', '$coverstockName', '$coverstockType', '$rgValue', '$diffValue', '$intDiffValue', '$ballStock')";
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