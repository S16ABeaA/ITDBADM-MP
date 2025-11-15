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
  if(!$accessoryId || empty($accessoryImage) || empty($accessoryName) || empty($accessoryBrand) || empty($handedness) || empty($accessoryType) || empty($accessoryPrice) || empty($accessoryStock) || empty($accessoryDescription)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to update the bowling accessory.'
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

  // Update bowling accessory
  $updatebgquery = "UPDATE bowlingaccessories SET Name='$accessoryName',  Type='$accessoryType', Handedness='$handedness' WHERE ProductID='$accessoryId'";
  $updatebgresult = $conn->query($updatebgquery);

  $updateproductquery = "UPDATE product SET Price='$accessoryPrice', Quantity='$accessoryStock', ImageID='$accessoryImage', BrandID='$accessoryBrand' WHERE ProductID='$accessoryId'";
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