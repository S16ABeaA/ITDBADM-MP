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
  $shoeDescription = $_POST['shoeDescription'] ?? '';
  $shoeImage = $_POST['shoeImage'] ?? '';
  // Validate required fields
  if(empty($shoeImage) || empty($shoeName) || empty($shoeBrand) || empty($shoeSize) || empty($shoeGender) || empty($shoePrice) || empty($shoeStock) || empty($shoeDescription)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the bowling shoe.'
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
  $bschecker = "SELECT * FROM bowlingshoes WHERE Name = '$shoeName' AND size = '$shoeSize' AND sex = '$shoeGender'";
  $bsresult = $conn->query($bschecker);
  
  if($bsresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This bowling shoe already exists.'
    ]);
    exit;
  }

  // Insert new bowling shoe
  $insertbsquery = "CALL AddBowlingShoes('1', '$shoeBrand', '$shoeName', '$shoePrice', '$shoeImage', '$shoeSize', '$shoeGender', '$shoeStock')";
  $insertbsresult = $conn->query($insertbsquery);
  
  if($insertbsresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Bowling shoe has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
        'shoeName' => $shoeName,
        'shoeBrand' => $shoeBrand,
        'shoePrice' => '₱' . number_format($shoePrice, 2),
        'shoeStock' => $shoeStock,
        'shoeImage' => $shoeImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert bowling shoe: ' . $conn->error
    ]);
  }
  exit;
}
?>