<?php
include '../include/connection.php';

if (isset($_POST['submit'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $confirmpassword = mysqli_real_escape_string($conn, $_POST['confirmpassword']);
  $profile = $_FILES['profile']['name'];
  $image_size = $_FILES['profile']['size'];
  $image_tmp_name = $_FILES['profile']['tmp_name'];
  $image_folder = $_SERVER['DOCUMENT_ROOT'] . '/EASE-CHOLAR/user_profiles/' . $profile;

  $role = 'Registrar';

  $select = mysqli_query($conn, "SELECT * FROM `tbl_registrar` WHERE email = '$email'") or die('query failed');

  if (mysqli_num_rows($select) > 0) {
    $emailExistsMessage = "Email Already exists!";
  } else {
    if ($password != $confirmpassword) {
      $passwordMismatchMessage = "Confirm password does not match!";
    } elseif ($image_size > 2000000) {
      $largeImageMessage = "Image size is too large!";
    } else {
      // Hash the password
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert = mysqli_query($conn, "INSERT INTO `tbl_registrar` (username, full_name, email, password, role, profile) VALUES ('$username', '$full_name', '$email', '$hashed_password', '$role', '$profile')") or die('Query failed: ' . mysqli_error($conn));

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
  <link rel="stylesheet" href="css/registrar_register.css">

  <title>AdminModule</title>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>

  <div class="background">
    <div class="info-logo">
      <div class="logo">
        <img class="img-responsive" src="../img/headerisu.png" alt="">
      </div>
      <div class="title">
        <span class="text">EASE-CHOLAR: A WEB-BASED SCHOLARSHIP APPLICATION MANANGEMENT SYSTEM</span>
      </div>
    </div>
  </div>

  <div class="log-in">
    <form class="form" action="" method="POST" enctype="multipart/form-data">
      <p class="form-title">REGISTRATION</p>
      <?php
      if (isset($emailExistsMessage)) {
        echo '<script>
                  swal("Email Exists", "' . $emailExistsMessage . '", "error");
                </script>';
      }

      if (isset($passwordMismatchMessage)) {
        echo '<script>
                  swal("Password Mismatch", "' . $passwordMismatchMessage . '", "error");
                </script>';
      }

      if (isset($largeImageMessage)) {
        echo '<script>
                  swal("Large Image", "' . $largeImageMessage . '", "error");
                </script>';
      }

      if (isset($successMessage)) {
        echo '<script>
                  swal({
                     title: "Success",
                     text: "' . $successMessage . '",
                     icon: "success",
                  }).then(function() {
                    window.location = "registrar_login.php";
                  });
                </script>';
      }

      if (isset($registrationFailedMessage)) {
        echo '<script>
                  swal("Registration Failed", "' . $registrationFailedMessage . '", "error");
                </script>';
      }
      ?>
      <div class="page-links">
        <a href="registrar_login.php">Login </a>
        <a href="registrar_register.php" class="active">Register</a>
      </div>

      <div class="input-container">
        <span class="input-container-addon">
          <i class="fa fa-user"></i>
        </span>
        <input class="input-style" id="username" type="text" name="username" placeholder="Enter your Username" required>
      </div>

      <div class="input-container">
        <span class="input-container-addon">
          <i class="fa fa-user"></i>
        </span>
        <input class="input-style" id="full_name" type="text" name="full_name" placeholder="First Name | Middle Name | Last Name" required>
      </div>

      <div class="input-container">
        <span class="input-container-addon">
          <i class="fa fa-envelope-square"></i>
        </span>
        <input class="input-style" id="email" type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="input-container">
        <span class="input-container-addon">
          <i class="fa fa-lock"></i>
        </span>
        <input class="input-style" id="password" type="password" name="password" placeholder="Enter your password" required>
      </div>

      <div class="input-container">
        <input class="input-style" type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm your password" required>
      </div>

      <div class="input-container">
        <span class="input-container-addon">
          <i class="fa fa-image"></i>
        </span>
        <input class="input-style" type="file" name="profile" placeholder="Profile pic" accept="image/jpg, image/jpeg, image/png" required>
      </div>

      <div class="button">
        <button type="submit" name="submit" class="submit">Submit</button>
      </div>
    </form>
  </div>
</body>

</html>