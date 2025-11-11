<?php
if(isset($_POST['insertedit_bb'])){
  // Retrieve form data
  $ballId = $_POST['ballID'];
  $ballName = $_POST['ballName'];
  $ballBrand = $_POST['ballBrand'];
  $ballPrice = $_POST['ballPrice'];
  $ballStock = $_POST['ballStock'];
  $ballWeight = $_POST['ballWeight'];
  $ballType = $_POST['ballType'];
  $ballQuality = $_POST['ballQuality'];
  $coreName = $_POST['coreName'];
  $coreType = $_POST['coreType'];
  $rgValue = $_POST['rgValue'];
  $diffValue = $_POST['diffValue'];
  $intDiffValue = $_POST['intDiffValue'];
  $coverstockName = $_POST['coverstockName'];
  $coverstockType = $_POST['coverstockType'];
  $ballDescription = $_POST['ballDescription'];
  $bb_image = $_FILES['ballImage']['name'];
  $xt = pathinfo($bb_image, PATHINFO_EXTENSION);
  $allowedtypes = array("jpg", "jpeg", "png", "gif", "webp");
  $tempname = $_FILES['ballImage']['tmp_name'];
  $target = "../images/".$bb_image;
  if(in_array($xt, $allowedtypes)){
    if(move_uploaded_file($tempname, $target)){
      if(empty($ballName) || empty($ballBrand) || empty($ballPrice) || empty($ballStock) || empty($ballWeight) || empty($ballType) || empty($ballQuality) || empty($coreName) || empty($coreType) || empty($rgValue) || empty($diffValue) || empty($intDiffValue) || empty($coverstockName) || empty($coverstockType))
        echo "<script>alert('Please fill all fields to insert the bowling ball.')</script>";
      else{
        $bbchecker = "SELECT * FROM bowlingball WHERE Quality = '$ballQuality' AND Name = '$ballName' AND Type = '$ballType' AND RG = '$rgValue' AND DIFF = '$diffValue' AND INTDIFF = '$intDiffValue' AND weight = '$ballWeight' AND CoreName = '$coreName' AND CoreType = '$coreType' AND Coverstock = '$coverstockName' AND CoverstockType = '$coverstockType'";
        $bbresult = $conn->query($bbchecker);
        if($bbresult->num_rows > 0){
          echo "<script>alert('This bowling ball already exists.')</script>";
        }
        else{
            $insertbbquery = "CALL AddBowlingBall('1', '$ballBrand', '$ballName', '$ballPrice', '$bb_image', '$ballQuality', '$ballType', '$ballWeight', '$coreType', '$coreName', '$coverstockName', '$coverstockType', '$rgValue', '$diffValue', '$intDiffValue', '$ballStock')";
            $insertbbresult = $conn->query($insertbbquery);
            if($insertbbresult){
              echo "<script>alert('Bowling ball has been added.')</script>";
            }
        }
      }
    }
  }
}
?>