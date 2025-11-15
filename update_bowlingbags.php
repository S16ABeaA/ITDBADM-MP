<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_bg'])){
  // Retrieve form data
  $bagId = $_POST['bagID'] ?? null;
  $bagName = $_POST['bagName'] ?? '';
  $bagBrand = $_POST['bagBrand'] ?? '';
  $bagColor = $_POST['bagColor'] ?? '';
  $bagType = $_POST['bagType'] ?? '';
  $bagPrice = $_POST['bagPrice'] ?? '';
  $bagStock = $_POST['bagStock'] ?? '';
  $bagDescription = $_POST['bagDescription'] ?? '';
  $bagImage = $_POST['bagImage'] ?? '';
  // Validate required fields
  if(!$bagId || empty($bagImage) || empty($bagName) || empty($bagBrand) || empty($bagColor) || empty($bagType) || empty($bagPrice) || empty($bagStock) || empty($bagDescription)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling bag.'
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


  // Update bowling bag
  $updatebgquery = "UPDATE bowlingbag SET Name='$bagName',  Type='$bagType', Size='$bagSize', color='$bagColor' WHERE ProductID='$bagId'";
  $updatebgresult = $conn->query($updatebgquery);

  $updateproductquery = "UPDATE product SET Price='$bagPrice', Quantity='$bagStock', ImageID='$bagImage', BrandID='$bagBrand' WHERE ProductID='$bagId'";
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