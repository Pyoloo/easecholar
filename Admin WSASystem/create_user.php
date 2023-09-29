<?php
include 'connection.php';

if (isset($_POST['submit'])) {
   $username = mysqli_real_escape_string($dbConn, $_POST['username']);
   $full_name = mysqli_real_escape_string($dbConn, $_POST['full_name']);
   $password = mysqli_real_escape_string($dbConn, $_POST['password']);
   $confirmpassword = mysqli_real_escape_string($dbConn, $_POST['confirmpassword']);
   $profile = $_FILES['profile']['name'];
   $image_size = $_FILES['profile']['size'];
   $image_tmp_name = $_FILES['profile']['tmp_name'];
   $image_folder = $_SERVER['DOCUMENT_ROOT'] . '/EASE-CHOLAR/user_profiles/' . $profile;

   $role = 'OSA';

  $query = mysqli_prepare($dbConn, "SELECT * FROM `tbl_admin` WHERE username = ? OR email = ?");
  mysqli_stmt_bind_param($query, "ss", $username, $email);
  mysqli_stmt_execute($query);
  $result = mysqli_stmt_get_result($query);


   if (mysqli_num_rows($result) > 0) {
      $emailExistsMessage = "Email or Username Already exists!";
   } else {
      if ($password != $confirmpassword) {
         $passwordMismatchMessage = "Confirm password does not match!";
      } elseif ($image_size > 2000000) {
         $largeImageMessage = "Image size is too large!";
      } else {
         // Hash the password
         $hashed_password = password_hash($password, PASSWORD_DEFAULT);

         $insert = mysqli_query($dbConn, "INSERT INTO `tbl_admin` (username, full_name, email, password, role, profile) VALUES ('$username', '$full_name', '$email', '$hashed_password', '$role', '$profile')") or die('Query failed: ' . mysqli_error($dbConn));

         if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $successMessage = 'Registered successfully!';
         } else {
            $registrationFailedMessage = 'Registration failed!';
         }
      }
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- Boxicons -->
   <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <!-- My CSS -->
   <link rel="stylesheet" href="css/create_user.css">
   <title>AdminModule</title>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
   <style>
    .selected-image-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .image-container {
            display: flex;
            justify-content: center;

        }

        #image-label {
            display: block;
            color: white;
            font-style: italic;

        }

        #selected-image {
            width: 60px;
            height: 60px;
            border-radius: 30px;
        }
   </style>
</head>
<body>
  
    <div class="background">
        <div class="info-logo">
            <div class="logo">
                <img class="img-responsive" src="/EASE-CHOLAR/headerisu.png" alt="">
            </div>
            <div class="title">
                <span class="text">EASE-CHOLAR: A WEB-BASED SCHOLARSHIP APPLICATION MANAGEMENT SYSTEM</span>
            </div>
        </div>
    </div>

    <div class="log-in">
        <form class="form" action="" method="POST" enctype="multipart/form-data">
            <p class="form-title">REGISTRATION</p>
            <?php
              if (isset($emailExistsMessage)) {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Email Exists",
                    text: "' . $emailExistsMessage . '",
                    showConfirmButton: false,
                    timer: 2000
                })
            </script>';
            }
              if (isset($passwordMismatchMessage)) {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Password Mismatch",
                    text: "' . $passwordMismatchMessage . '",
                    showConfirmButton: false,
                    timer: 2000
                })
            </script>';
            }
              if (isset($largeImageMessage)) {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Large Image",
                    text: "' . $largeImageMessage . '",
                    showConfirmButton: false,
                    timer: 2000
                })
            </script>';
            }

              if (isset($successMessage)) {
                echo '<script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "' . $successMessage . '",
                    showConfirmButton: false,
                    timer: 2500
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        window.location.href = "manage_users.php";
                    }
                });
                </script>';
            }
              if (isset($registrationFailedMessage)) {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Registration Failed",
                    text: "' . $registrationFailedMessage . '",
                    showConfirmButton: false,
                    timer: 2000
                })
            </script>';
            }
             ?>
            <div class="page-links">
            <a href="create_user.php" class="active">OSA</a>
                <a href="new_registrar.php">REGISTRAR </a>
                
            </div>

            <div class="selected-image-container">
                <div class="image-container">
                    <img id="selected-image" src="/EASE-CHOLAR/default-avatar.png" alt="Selected Image">
                </div>
            </div>

            <div class="input-container">
                <span class="input-container-addon">
                    <i class="fa fa-image"></i>
                </span>
                <input class="input-style" type="file" name="profile" placeholder="Profile pic" accept="image/jpg, image/jpeg, image/png">
            </div>

            <div class="input-container">
                <span class="input-container-addon">
                    <i class="fa fa-user"></i>
                </span>
                <input class="input-style" id="full_name" type="text" name="full_name" placeholder="First Name | Middle Name | Last Name" required>
            </div>

            <div class="input-container">
                <span class="input-container-addon">
                    <i class="fa fa-user"></i>
                </span>
                <input class="input-style" id="username" type="text" name="username" placeholder="Enter username" required>
            </div>

            <div class="input-container">
                <span class="input-container-addon">
                    <i class="fa fa-lock"></i>
                </span>
                <input class="input-style" id="password" type="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="input-container">
                <input class="input-style" type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm password" required>
            </div>


            <div class="button">
                <button type="submit" name="submit" class="submit">Submit</button>
            </div>
        </form>
    </div>

    <script>
      // Function to display the selected image and control label visibility
      function displaySelectedImage() {
            var input = document.getElementById('image-input');
            var selectedImage = document.getElementById('selected-image');
            var imageLabel = document.getElementById('image-label');

            input.addEventListener('change', function() {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        selectedImage.src = e.target.result;
                        selectedImage.style.display = 'block'; // Show the selected image
                        imageLabel.style.display = 'none'; // Hide the label
                    };

                    reader.readAsDataURL(input.files[0]);
                } else {
                    selectedImage.src = ""; // Clear the selected image if no file is selected
                    selectedImage.style.display = 'none'; // Hide the selected image
                    imageLabel.style.display = 'block'; // Show the label
                }
            });
        }

        // Call the function to display the selected image
        displaySelectedImage();
    </script>
</body>
</html>