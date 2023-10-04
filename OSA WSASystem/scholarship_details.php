<?php
include('../include/connection.php');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_errno);
}

if (isset($_GET['id'])) {
    $scholarshipId = $_GET['id'];
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
            <style>
                h2{
                    text-align: center;
                }
                .status-row {
                    display: flex;
                }

                .status-label {
                    flex: 0.3;
                    text-align: center;
                    align-items: center;
                    display: flex;
                    justify-content: center;
                    border-radius: 80px;
                    margin: 50px;
                }

                .status-description {
                    flex: 3;
                    font-size: 15px;
                    margin: 20px;
                    text-align: justify;
                    display: flex;
                    align-items: center;
                }

                .status-pending {
                    padding: 2px 0px;
                    background-color: var(--orange);
                    font-weight: 600;
                    color: #fff;
                    font-size: 15px;
                }

                .status-inreview {
                    background-color: var(--yellow);
                    font-weight: 600;
                    color: #fff;
                    font-size: 15px;
                }

                .status-qualified {
                    background-color: #00d084;
                    font-weight: 600;
                    color: #fff;
                    font-size: 15px;
                }

                .status-accepted {
                    background-color: #28a745;
                    font-weight: 600;
                    color: #fff;
                    font-size: 15px;
                }

                .status-rejected {
                    background-color: red;
                    font-weight: 600;
                    color: #fff;
                    font-size: 15px;
                }

                .status-details {
                    cursor: pointer;
                    font-weight: 600;
                    color: blue;
                }

                /* Modal */
                .modal {
                    display: none;
                    /* Hide the modal by default */
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);
                    /* Semi-transparent background overlay */
                    z-index: 1000;
                    /* Ensure the modal is on top of other elements */
                    overflow: auto;
                }

                /* Modal Content */
                .modal-content {
                    background-color: #fff;
                    margin: 15% auto;
                    /* Center the modal vertically */
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
                    max-width: 80%;
                    /* Adjust the maximum width as needed */
                }

                /* Close Button */
                .close {
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    font-size: 20px;
                    font-weight: bold;
                    cursor: pointer;
                }

                /* Add any additional styling as needed */
            </style>
        </head>
        <?php include('../include/header.php') ?>

        <body>
            <div class="table-data">
                <h1><?php echo $row['scholarship']; ?></h1>
                <hr>
                <div class="scholarship-details"> <?php echo $row['details']; ?></div>
                <h3>Requirements:</h3>
                <ul>
                    <?php
                    foreach ($requirements as $requirement) {
                        echo "<li>$requirement</li>";
                    }
                    ?>
                </ul>
                <h3>Benefits:</h3>
                <ul>
                    <?php
                    foreach ($benefits as $benefit) {
                        echo "<li>$benefit</li>";
                    }
                    ?>
                </ul>

                <div class="faq-content">
                    <label class="how-to-apply">How to apply for the Scholarship? </label>
                    <p class="guidelines">All applicants should fill up the application form. Provide a clear information and details. Upon submitting the Application Form wait for the OSA or committee to process your application.</p>
                </div>

                <div class="faq-content">
                    <label class="how-to-apply">How to know the status of your application? </label>
                    <p class="guidelines">To check the status of your application, log in to your account and navigate to the '<a class="aplication-status">Application Status</a>' section, where you can view whether your application is in one of the following states: Pending, In Review, Qualified, Accepted, or Rejected. Click <span class="status-details" onclick="showStatusInfo()">Status Details</span> for more information.</p>
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