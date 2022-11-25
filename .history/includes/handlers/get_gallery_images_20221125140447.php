  <?php

  
  if(isset($_POST["submitGallery"])) {
  
    $uploadOk = 1;
    $imageName = $_FILES['galleryUpload']['name'];
    $errorMessage = "";
  
    if($imageName !== "") {
  
      if (!file_exists("assets/images/galleries/$username")) {
        mkdir("assets/images/galleries/$username", 0755, true);
      }
  
      $targetDir = "assets/images/galleries/$username/";
      $imageName = $targetDir . uniqid() . basename($imageName);
      $imageName = str_replace(" ", "_", $imageName);
      $imageName = str_replace(",", "_", $imageName);
      $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
  
      if($_FILES['galleryUpload']['size'] > 1000000) {
        $errorMessage = "Sorry, your file is too large";
        $uploadOk = 0;
      }
  
      if(strtolower($imageFileType) !== "jpeg" && strtolower($imageFileType) !== "png" && strtolower($imageFileType) !== "jpg") {
        $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
        $uploadOk = 0;
      }
  
      if($uploadOk) {
        
        if(move_uploaded_file($_FILES['galleryUpload']['tmp_name'], $imageName)) {
  
          $set_gallery = mysqli_query($con, "UPDATE user SET gallery=CONCAT(gallery, '$imageName', ',') WHERE username='$userLoggedIn'");
          
        }
        else {
          $uploadOk = 0;
          $errorMessage = "There has been an error. Please try again";
          echo "<div style='text-align:center;' class='alert alert-danger'>
              $errorMessage
            </div>";
        }
      }
  
      else {
  
        echo "<div style='text-align:center;' class='alert alert-danger'>
              $errorMessage
            </div>";
      }
  
    }
  
    else {
  
    echo "<div style='text-align:center;' class='alert alert-danger'>
              Please select an image first
            </div>";
  
    }
    
    $link = '#profileTabs a[href="#gallery_div"]';
  
    echo "<script>
            $(function() {
              $('" . $link ."').tab('show');
            });
          </script>";
  
  }

  ?>