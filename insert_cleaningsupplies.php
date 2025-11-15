<?php
header('Content-Type: application/json');
require_once __DIR__ . '/dependencies/config.php';

if(isset($_POST['insertedit_cs'])){
  // Retrieve form data
  $cleaningId = $_POST['cleaningID'] ?? null;
  $supplyName = $_POST['supplyName'] ?? '';
  $supplyBrand = $_POST['supplyBrand'] ?? '';
  $supplyType = $_POST['supplyType'] ?? '';
  $supplyPrice = $_POST['supplyPrice'] ?? '';
  $supplyStock = $_POST['supplyStock'] ?? '';
  $supplyDescription = $_POST['supplyDescription'] ?? '';
  $supplyImage = $_POST['supplyImage'] ?? '';
  // Validate required fields
  if(empty($supplyImage) || empty($supplyName) || empty($supplyBrand) || empty($supplyType) || empty($supplyPrice) || empty($supplyStock) || empty($supplyDescription)){
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Please fill all fields to insert the cleaning supply.'
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
  $cschecker = "SELECT * FROM cleaningsupplies WHERE Name = '$supplyName' AND Type = '$supplyType'";
  $csresult = $conn->query($cschecker);
  
  if($csresult->num_rows > 0){
    http_response_code(409);
    echo json_encode([
      'success' => false,
      'message' => 'This cleaning supply already exists.'
    ]);
    exit;
  }

  // Insert new bowling bag
  $insertcsquery = "CALL AddCleaningSupplies('1', '$supplyBrand', '$supplyName', '$supplyPrice', '$supplyImage', '$supplyType', '$supplyStock')";
  $insertcsresult = $conn->query($insertcsquery);
  
  if($insertcsresult){
    // Get the newly inserted product ID
    $lastIdQuery = "SELECT MAX(ProductID) as productId FROM product";
    $lastIdResult = $conn->query($lastIdQuery);
    $lastIdRow = $lastIdResult->fetch_assoc();
    $newProductId = $lastIdRow['productId'];
    
    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Cleaning supply has been added successfully.',
      'productId' => $newProductId,
      'data' => [
        'productId' => $newProductId,
        'supplyName' => $supplyName,
        'supplyBrand' => $supplyBrand,
        'supplyPrice' => '₱' . number_format($supplyPrice, 2),
        'supplyType' => $supplyType,
        'supplyStock' => $supplyStock,
        'supplyImage' => $supplyImage
      ]
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to insert cleaning supply: ' . $conn->error
    ]);
  }
  exit;
}
?>