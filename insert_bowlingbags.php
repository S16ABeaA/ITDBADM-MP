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
  if(empty($bagImage) || empty($bagName) || empty($bagBrand) || empty($bagColor) || empty($bagType) || empty($bagPrice) || empty($bagStock) || empty($bagDescription)){
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

  // Check if bowling shoe already exists
  $bgchecker = "SELECT * FROM bowlingbag WHERE Name = '$bagName' AND Type = '$bagType' AND Size = '$bagSize' AND color = '$bagColor'";
  $bgresult = $conn->query($bgchecker);
  
  if($bgresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This bowling bag already exists.'
    ]);
    exit;
  }

  // Insert new bowling bag
  $insertbgquery = "CALL AddBowlingBag('1', '$bagBrand', '$bagName', '$bagPrice', '$bagImage', '$bagType', '$bagSize', '$bagColor', '$bagStock')";
  $insertbgresult = $conn->query($insertbgquery);
  
  if($insertbgresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling bag has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
        'bagName' => $bagName,
        'bagBrand' => $bagBrand,
        'bagPrice' => '₱' . number_format($bagPrice, 2),
        'bagType' => $bagType,
        'bagSize' => $bagSize,
        'bagColor' => $bagColor,
        'bagStock' => $bagStock,
        'bagImage' => $bagImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert bowling bag: ' . $conn->error
    ]);
  }
  exit;
}
?>