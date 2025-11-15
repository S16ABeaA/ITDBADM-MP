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
  if(!$shoeId || empty($shoeImage) || empty($shoeName) || empty($shoeBrand) || empty($shoeSize) || empty($shoeGender) || empty($shoePrice) || empty($shoeStock) || empty($shoeDescription)){
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

  // Update bowling shoe
  $updatebsquery = "UPDATE bowlingshoes SET Name='$shoeName',  size='$shoeSize', sex='$shoeGender' WHERE ProductID='$shoeId'";
  $updatebsresult = $conn->query($updatebsquery);

  $updateproductquery = "UPDATE product SET Price='$shoePrice', Quantity='$shoeStock', ImageID='$shoeImage', BrandID='$shoeBrand' WHERE ProductID='$shoeId'";
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