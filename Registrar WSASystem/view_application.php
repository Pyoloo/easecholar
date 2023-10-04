<?php
include '../include/connection.php';
session_name("RegistrarSession");
session_start();

// Check if 'registrar_id' is not set in the session, redirect to login page
if (!isset($_SESSION['registrar_id'])) {
    header('location: registrar_login.php');
    exit();
}

$registrar_id = $_SESSION['registrar_id'];

if (isset($_GET['logout'])) {
    unset($registrar_id);
    session_destroy();
    header('location: registrar_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $application_id = $_GET['id'];

    // Retrieve application details from 'tbl_userapp' using prepared statement
    $query = "SELECT * FROM `tbl_userapp` WHERE `application_id` = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        echo "Error preparing query: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $application_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo "Error executing query: " . mysqli_error($conn);
        exit();
    }

    if (mysqli_num_rows($result) == 0) {
        echo "Application not found.";
        exit();
    }

    $applicationData = mysqli_fetch_assoc($result);

    // Retrieve status from 'tbl_userapp' using prepared statement
    $statusQuery = "SELECT `grade_status` FROM `tbl_userapp` WHERE `application_id` = ?";
    $statusStmt = mysqli_prepare($conn, $statusQuery);

    if (!$statusStmt) {
        echo "Error preparing query: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($statusStmt, "i", $application_id);
    mysqli_stmt_execute($statusStmt);
    $statusResult = mysqli_stmt_get_result($statusStmt);

    if (!$statusResult) {
        echo "Error executing query: " . mysqli_error($conn);
        exit();
    }

    $statusData = mysqli_fetch_assoc($statusResult);
    $grade_status = $statusData['grade_status'];
} else {
    echo "Application ID not provided.";
    exit();
}

// Handle form submission for sending messages
if (isset($_POST['message_content'])) {
    $message_content = $_POST['message_content'];

    // Insert the message into 'tbl_user_messages' using prepared statement
    $insertQuery = "INSERT INTO `tbl_user_messages` (`application_id`, `registrar_id`, `message_content`, `sent_at`)
                    VALUES (?, ?, ?, NOW())";
    $insertStmt = mysqli_prepare($conn, $insertQuery);

    if (!$insertStmt) {
        echo "Error preparing query: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($insertStmt, "iis", $application_id, $registrar_id, $message_content);
    $insertResult = mysqli_stmt_execute($insertStmt);

    if ($insertResult) {
        // Message successfully sent, you can add any success message or redirection here
        echo "Message Sent";
        header("Location: view_application.php?id=$application_id");
        exit();
    } else {
        echo "Error sending message: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
    <link rel="stylesheet" href="css/view_application.css">
</head>

<body>
    <?php include('../include/header.php'); ?>

    <hr>

    <div class="wrapper">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="container">
                <div class="head">
                    <div class="img"><img src="/EASE-CHOLAR/user_profiles/<?php echo $applicationData['image']; ?>" alt="Profile"></div>
                    <p>Applicant Name: <?php echo $applicationData['applicant_name']; ?></p>
                    <div class="reminder">
                        <h2>Status: <?php echo $status; ?></h2>
                        <span class="remind">*Please update the applicant status</span>

                        <form method="post" action="view_application.php?id=<?php echo $application_id; ?>">
                            <label for="status">Status:</label>
                            <select name="status" id="status">
                                <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="In Review" <?php if ($status == 'In Review') echo 'selected'; ?>>In Review</option>
                                <option value="Qualified" <?php if ($status == 'Qualified') echo 'selected'; ?>>Qualified</option>
                                <option value="Accepted" <?php if ($status == 'Accepted') echo 'selected'; ?>>Accepted</option>
                                <option value="Rejected" <?php if ($status == 'Rejected') echo 'selected'; ?>>Rejected</option>
                            </select>

                            <button type="submit">Update</button>
                        </form>
                    </div>
                </div>
                <div class="form-first">
                    <h3 style="color:darkgreen">PERSONAL INFORMATION:</h3>
                    <br>
                    <div class="details personal">
                        <div class="fields">
                            <div class="input-field">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo $applicationData['last_name']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo $applicationData['first_name']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="<?php echo $applicationData['middle_name']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" value="<?php echo $applicationData['dob']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label>Place of Birth</label>
                                <input type="text" name="pob" placeholder="Enter birth date" value="<?php echo $applicationData['pob']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label>Gender</label>
                                <select name="gender" disabled>
                                    <option><?php echo $applicationData['gender']; ?></option>
                                </select>
                            </div>
                        </div>


                        <div class="input-field">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $applicationData['email']; ?>" disabled>
                        </div>
                        <div class="fields">
                            <div class="input-field">
                                <label>School ID Number</label>
                                <input type="text" name="id_number" value="<?php echo $applicationData['id_number']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label>Mobile Number</label>
                                <input type="number" name="mobile_num" value="<?php echo $applicationData['mobile_num']; ?>" disabled>
                            </div>
                            <div class="input-field">
                                <label>Citizenship</label>
                                <input type="text" name="citizenship" value="<?php echo $applicationData['citizenship']; ?>" disabled>
                            </div>
                        </div>

                        <div class="form-second">
                            <div class="input-field">
                                <h3 style="color:darkgreen">PERMANENT ADDRESS</h3>
                                <div class="address-inputs">
                                    <input type="text" name="barangay" value="<?php echo $applicationData['barangay']; ?>" disabled>
                                    <input type="text" name="town_city" value="<?php echo $applicationData['town_city']; ?>" disabled>
                                    <input type="text" name="province" value="<?php echo $applicationData['province']; ?>" disabled>
                                    <input type="number" name="zip_code" value="<?php echo $applicationData['zip_code']; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-second">
                    <h3 style="color:darkgreen">FAMILY BACKGROUND:</h3>
                    <div class="details family">
                        <div class="fields-info">
                            <div class="form">
                                <div class="input-field">
                                    <span class="title"> FATHER </span>
                                    <hr>
                                    <label>Name</label>
                                    <input type="text" name="father_name" value="<?php echo $applicationData['father_name']; ?>" disabled>
                                    <label>Address</label>
                                    <input type="text" name="father_address" placeholder="Enter address" value="<?php echo $applicationData['father_address']; ?>" disabled>
                                    <label>Occupation</label>
                                    <input type="text" name="father_work" value="<?php echo $applicationData['father_work']; ?>" disabled>
                                </div>
                            </div>

                            <div class="form">
                                <div class="input-field">
                                    <span class="title"> MOTHER </span>
                                    <hr>
                                    <label>Name</label>
                                    <input type="text" name="mother_name" value="<?php echo $applicationData['mother_name']; ?>" disabled>
                                    <label>Address</label>
                                    <input type="text" name="mother_address" placeholder="Enter address" value="<?php echo $applicationData['mother_address']; ?>" disabled>
                                    <label>Occupation</label>
                                    <input type="text" name="mother_work" placeholder="Enter Occupation" value="<?php echo $applicationData['mother_work']; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Other Documents</h3>
                <?php
                // Assuming $applicationData['file'] contains comma-separated file names
                if (!empty($applicationData['file'])) {
                    $fileNames = explode(',', $applicationData['file']);
                    foreach ($fileNames as $fileName) {
                        $filePath = '/EASE-CHOLAR/file_uploads/' . $fileName;
                        // Update the file path
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filePath)) {
                            echo '<p>File: <a href="' . $filePath . '" target="_blank">' . $fileName . '</a></p>';
                        } else {
                            echo '<p>File path not found: ' . $filePath . '</p>';
                        }
                    }
                }
                ?>

                <hr>
        <div class="message-box">
            <h3>Send Message to Applicant</h3>
            <form method="post" action="send_applicant_message.php">
                <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                <input type="hidden" name="registrar_id" value="<?php echo $registrar_id; ?>"> <!-- Add this line -->
                <label for="message_content">Message:</label>
                <textarea name="reg_message_content" id="reg_message_content" rows="4" cols="50"></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

</body>

</html>