<?php
include '../include/connection.php';
session_name("OsaSession");
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location: osa_login.php');
    exit();
}

if (isset($_GET['logout'])) {
    unset($admin_id);
    session_destroy();
    header('location: osa_login.php');
    exit();
}
// Add the database connection information here
$dbHost = "localhost"; // Replace with your database host
$dbUser = "root"; // Replace with your database username
$dbPass = ""; // Replace with your database password
$dbName = "easecholar"; // Replace with your database name

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_num = $_POST['phone_num'];

    $errors = array();

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required.';
    }

    $profile = $_FILES['profile'];

    if (!empty($profile['name'])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($profile['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Invalid file type. Allowed types: jpg, jpeg, png, gif';
        }

        $file_name = uniqid('profile_') . '.' . $file_extension;
        $upload_directory = $_SERVER['DOCUMENT_ROOT'] . '/user_profiles/' . $file_name;

        if (move_uploaded_file($profile['tmp_name'], $upload_directory)) {
            // Store only the file name in the database
            $profile_path = $file_name;

            // Update the user's profile in the database
            $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "UPDATE tbl_admin SET email = ?, phone_num = ?, profile = ?, username = ?, full_name = ? WHERE admin_id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // Bind parameters
                $stmt->bind_param("sssssi", $email, $phone_num, $profile_path, $username, $full_name, $admin_id);

                if ($stmt->execute()) {
                    // Profile updated successfully
                } else {
                    $errors[] = 'Profile update failed.';
                }

                $stmt->close();
            } else {
                $errors[] = 'Statement preparation failed.';
            }

            $conn->close();
        } else {
            $errors[] = 'File upload failed.';
        }
    }

    if (empty($errors)) {
        // Construct the updated profile image HTML
        $updatedProfileImageHTML = '';
        if (!empty($profile_path)) {
            $updatedProfileImageHTML = "<img src='../user_profiles/{$profile_path}' width='150' height='150'>";
        }

        // Return the updated profile image HTML as the response
        echo $updatedProfileImageHTML;
    } else {
        foreach ($errors as $error) {
            echo "<p>{$error}</p>";
        }
    }
}
?>
