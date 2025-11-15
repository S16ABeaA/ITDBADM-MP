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
  $accessoryDescription = $_POST['accessoryDescription'] ?? '';
  $accessoryImage = $_POST['accessoryImage'] ?? '';
  // Validate required fields
  if(empty($accessoryImage) || empty($accessoryName) || empty($accessoryBrand) || empty($handedness) || empty($accessoryType) || empty($accessoryPrice) || empty($accessoryStock) || empty($accessoryDescription)){
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

  // Insert new bowling bag
  $insertbaquery = "CALL AddBowlingAccessories('1', '$accessoryBrand', '$accessoryName', '$accessoryPrice', '$accessoryImage', '$accessoryType', '$handedness', '$accessoryStock')";
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
        'accessoryImage' => $accessoryImage
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