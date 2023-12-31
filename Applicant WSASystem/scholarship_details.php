<?php
session_name("ApplicantSession");
session_start();
include('../include/connection.php');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_errno);
}

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $scholarshipId = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Query to check if the user has already applied for this scholarship
    $sqlCheckApplication = "SELECT * FROM tbl_userapp WHERE user_id = ? AND scholarship_id = ?";
    $stmtCheckApplication = $conn->prepare($sqlCheckApplication);
    $stmtCheckApplication->bind_param("ii", $user_id, $scholarshipId);
    $stmtCheckApplication->execute();
    $resultCheckApplication = $stmtCheckApplication->get_result();

    if ($resultCheckApplication->num_rows > 0) {
        // The applicant has already applied, display the message
        $applicationStatus = "You have already applied for this scholarship.";
        $showApplyButton = false; // Do not show the "APPLY" button
    } else {
        // Check the scholarship status
        $sqlCheckScholarshipStatus = "SELECT scholarship_status FROM tbl_scholarship WHERE scholarship_id = ?";
        $stmtCheckScholarshipStatus = $conn->prepare($sqlCheckScholarshipStatus);
        $stmtCheckScholarshipStatus->bind_param("i", $scholarshipId);
        $stmtCheckScholarshipStatus->execute();
        $resultCheckScholarshipStatus = $stmtCheckScholarshipStatus->get_result();

        if ($resultCheckScholarshipStatus->num_rows > 0) {
            $row = $resultCheckScholarshipStatus->fetch_assoc();
            $scholarshipStatus = $row['scholarship_status'];

            if ($scholarshipStatus === 'Closed') {
                // The scholarship is closed, display a message
                $applicationStatus = "This scholarship is closed and no longer accepting applications.";
                $showApplyButton = false; // Do not show the "APPLY" button
            } else {
                // The scholarship is ongoing, set a default message for cases where the user hasn't applied
                $applicationStatus = "";
                $showApplyButton = true; // Show the "APPLY" button
            }
        } else {
            // Scholarship status not found, handle as needed
            $applicationStatus = "Scholarship status not found.";
            $showApplyButton = false; // Do not show the "APPLY" button
        }
    }


    $sql = "SELECT * FROM tbl_scholarship WHERE scholarship_id = $scholarshipId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $details = $row['details'];
        $requirements = explode("\n", $row['requirements']);
        $benefits = explode("\n", $row['benefits']);
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="css/scholarship_details.css">

            <title>Scholarship Details</title>
        </head>
        <?php include('../include/header.php') ?>

        <body>
            <div class="table-data">
                <h1 class="scholarship-title"><?php echo $row['scholarship']; ?></h1>
                <hr>
                <div class="scholarship-details"> <?php echo $row['details']; ?></div>
                <div class="details-container">
                    <h4 class="details-label">Requirements:</h4>

                    <ul>
                        <?php
                        foreach ($requirements as $requirement) {
                            echo "<li>$requirement</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="details-container">
                    <h4 class="details-label">Benefits:</h4>

                    <ul>
                        <?php
                        foreach ($benefits as $benefit) {
                            echo "<li>$benefit</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="faq-content">
                    <label class="how-to-apply">How to apply for the Scholarship? </label>
                    <p class="guidelines">All applicants should fill up the application form. Provide a clear information and details. Upon submitting the Application Form wait for the OSA or committee to process your application.</p>
                </div>

                <div class="faq-content">
                    <label class="how-to-apply">How to know the status of your application? </label>
                    <p class="guidelines">To check the status of your application, log in to your account and navigate to the '<a class="aplication-status" href="application_status.php">Application Status</a>' section, where you can view whether your application is in one of the following states: Pending, In Review, Qualified, Accepted, or Rejected. Click <span class="status-details" onclick="showStatusInfo()">Status Details</span> for more information.</p>
                </div>

                <div id="statusInfoModal" class="modal">
                    <div class="modal-content">
                        <h2>Status Information</h2>
                        <div class="status-row">
                            <div class="status-label status-pending">Pending</div>
                            <div class="status-description">This status indicates that your application has been received but has not yet been reviewed or processed. It's awaiting initial assessment.</div>
                        </div>
                        <div class="status-row">
                            <div class="status-label status-inreview">In Review</div>
                            <div class="status-description">Your application is actively being evaluated by the scholarship committee or administrators. They are assessing your eligibility and qualifications.</div>
                        </div>
                        <div class="status-row">
                            <div class="status-label status-qualified">Qualified</div>
                            <div class="status-description">If your application is marked as "Qualified," it suggests that you meet the eligibility criteria and have advanced to the next stage of consideration.</div>
                        </div>
                        <div class="status-row">
                            <div class="status-label status-accepted">Accepted</div>
                            <div class="status-description">Congratulations, if your application status is "Accepted," it means you have been selected as a recipient of the scholarship. You may receive further instructions on how to claim the award.</div>
                        </div>
                        <div class="status-row">
                            <div class="status-label status-rejected">Rejected</div>
                            <div class="status-description">Unfortunately, this status means that your application was not chosen for the scholarship. You may receive feedback on why your application was not successful.</div>
                        </div>
                    </div>
                </div>



                <p class="alert-message"><?php echo $applicationStatus; ?></p>
                <?php if ($showApplyButton) { ?>
                    <a class="button" href="apply.php?id=<?php echo $scholarshipId; ?>&user_id=<?php echo $_SESSION['user_id']; ?>">APPLY</a>
                <?php } ?>
            </div>

    <?php
    } else {
        echo "No scholarship found with the specified ID.";
    }
} else {
    echo "Invalid request or not logged in.";
}
    ?>
    <script>
        // JavaScript function to show the status information modal
        function showStatusInfo() {
            var statusInfoModal = document.getElementById('statusInfoModal');
            statusInfoModal.style.display = 'block';

            // Close the modal if the user clicks outside of it
            window.onclick = function(event) {
                if (event.target == statusInfoModal) {
                    statusInfoModal.style.display = 'none';
                }
            };
        }
    </script>
        </body>

        </html>