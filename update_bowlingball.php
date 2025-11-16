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

  // Update bowling ball
  $updatebbquery = "UPDATE bowlingball SET Quality='$ballQuality', Name='$ballName', Type='$ballType', RG='$rgValue', DIFF='$diffValue', INTDIFF='$intDiffValue', weight='$ballWeight', CoreType='$coreType', CoreName='$coreName', Coverstock='$coverstockName', CoverstockType='$coverstockType' WHERE ProductID='$ballId'";
  $updatebbresult = $conn->query($updatebbquery);
  
  $updateproductquery = "UPDATE product SET Price='$ballPrice', Quantity='$ballStock', ImageID='$ballImage', BrandID='$ballBrand' WHERE ProductID='$ballId'";
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