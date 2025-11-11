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
      $updatebbquery= "UPDATE bowlingball SET Quality='$ballQuality', Name='$ballName', Type='$ballType', RG='$rgValue', DIFF='$diffValue', INTDIFF='$intDiffValue', weight='$ballWeight', CoreType='$coreType', CoreName='$coreName', Coverstock='$coverstockName', CoverstockType='$coverstockType' WHERE ProductID='$ballId'";
      $updatebbresult = $conn->query($updatebbquery);
      $updateproductquery = "UPDATE product SET Price='$ballPrice', Quantity='$ballStock', ImageID='$bb_image', BrandID='$ballBrand' WHERE ProductID='$ballId'";
      $updateproductresult = $conn->query($updateproductquery);
      if($updatebbresult && $updateproductresult){
        echo "<script>alert('Bowling ball has been updated.')</script>";
      }
    }
  }
}
?>