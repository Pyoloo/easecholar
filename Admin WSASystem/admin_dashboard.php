<?php
include '../include/connection.php';
session_name("AdminSession");
session_start();
$super_admin_id = $_SESSION['super_admin_id'];

if (!isset($super_admin_id)) {
    header('location: admin_login.php');
    exit();
}

if (isset($_GET['logout'])) {
    unset($super_admin_id);
    session_destroy();
    header('location: admin_login.php');
    exit();
}

// No need to include the connection.php again here
$select = mysqli_query($conn, "SELECT * FROM tbl_userapp WHERE status = 'Pending'") or die('query failed');

// Execute SQL queries to fetch counts for each status
$pendingCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_userapp WHERE status = 'Pending'")->fetch_assoc()['count'];
$inReviewCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_userapp WHERE status = 'In Review'")->fetch_assoc()['count'];
$qualifiedCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_userapp WHERE status = 'Qualified'")->fetch_assoc()['count'];
$acceptedCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_userapp WHERE status = 'Accepted'")->fetch_assoc()['count'];
$rejectedCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_userapp WHERE status = 'Rejected'")->fetch_assoc()['count'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <title>adminModule</title>

</head>

<body>




    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <img src="../img/isulogo.png">
            <span class="admin-hub">ADMIN</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="#">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="scholarship_list.php">
                    <i class='bx bxs-shopping-bag-alt'></i>
                    <span class="text">Scholarship</span>
                </a>
            </li>
            <li>
                <a href="manage_users.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="application_list.php">
                    <i class='bx bxs-file'></i>
                    <span class="text">Application List</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="#" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text" onclick="confirmLogout()">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <div class="menu">
                <i class='bx bx-menu'></i>
                <span class="school-name">ISABELA STATE UNIVERSITY SANTIAGO</span>
            </div>
            <div class="right-section">
                <div class="notif">
                    <div class="notification">
                        <?php
                        $getNotificationCountQuery = mysqli_query($conn, "SELECT COUNT(*) as count FROM tbl_admin_notif WHERE is_read = 'unread'") or die('query failed');
                        $notificationCountData = mysqli_fetch_assoc($getNotificationCountQuery);
                        $notificationCount = $notificationCountData['count'];


                        // Show the notification count only if there are new messages
                        if ($notificationCount > 0) {
                            echo '<i id="bellIcon" class="bx bxs-bell"></i>';
                            echo '<span class="num">' . $notificationCount . '</span>';
                        } else {
                            echo '<i id="bellIcon" class="bx bxs-bell"></i>';
                            echo '<span class="num" style="display: none;">' . $notificationCount . '</span>';
                        }
                        ?>
                    </div>

                    <?php
                    function formatCreatedAt($dbCreatedAt)
                    {
                        $dateTimeObject = new DateTime($dbCreatedAt);
                        return $dateTimeObject->format('Y-m-d, g:i A');
                    }
                    ?>

                    <div class="dropdown">
                        <?php
                        $notifications = mysqli_query($conn, "SELECT * FROM tbl_admin_notif WHERE is_read = 'unread'") or die('query failed');
                        ?>
                        <?php while ($row = mysqli_fetch_assoc($notifications)) { ?>
                            <div class="notify_item">
                                <div class="notify_img">
                                    <img src='../user_profiles/<?php echo $row['image']; ?>' alt="" style="width: 50px">
                                </div>
                                <div class="notify_info">
                                    <p><?php echo $row['message']; ?></p>
                                    <span class="notify_time"><?php echo formatCreatedAt($row['created_at']); ?></span>
                                </div>
                                <div class="notify_options">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    <!-- Add the ellipsis (three-dots) icon and the options menu -->
                                    <div class="options_menu">
                                        <span class="delete_option" data-notification-id="<?php echo $row['notification_id']; ?>">Delete</span>
                                        <span class="cancel_option">Cancel</span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                </div>
                <div class="profile">
                    <a href="admin_profile.php" class="profile">
                        <?php
                        $select_admin = mysqli_query($conn, "SELECT * FROM `tbl_super_admin` WHERE super_admin_id = '$super_admin_id'") or die('query failed');
                        $fetch = mysqli_fetch_assoc($select_admin);
                        if ($fetch && $fetch['profile'] != '') {
            
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/user_profiles/' . $fetch['profile'];

                            if (file_exists($imagePath)) {
                                echo '<img src="' . $imagePath . '">';
                            } else {
                                echo '<img src="../user_profiles/isulogo.png">';
                            }
                        } else {
                            echo '<img src="../user_profiles/isulogo.png">';
                        }
                        ?>
                    </a>

                </div>
            </div>
        </nav>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="box-info">
                <li>
                    <i class='bx bxs-calendar-check'></i>
                    <?php include('../include/connection.php'); ?>

                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM tbl_scholarship WHERE scholarship = 'Ongoing'");
                    $num_rows = mysqli_num_rows($result);
                    ?>
                    <a href="scholarship_list.php">
                    <span class="text">
                        <h3><?php echo $num_rows; ?></h3>
                        <p>Available Scholarships </p>
                    </span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-group'></i>
                    <?php include('../include/connection.php'); ?>

                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM tbl_userapp");
                    $num_rows = mysqli_num_rows($result);
                    ?>
                    
                    <a href="manage_users.php">
                    <span class="text">
                        <h3><?php echo $num_rows; ?></h3>
                        <p>System Users</p>
                    </span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-receipt'></i>
                    <?php include('../include/connection.php'); ?>

                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM tbl_userapp");
                    $num_rows = mysqli_num_rows($result);
                    ?>
                    <a href="application_list.php">
                    <span class="text">
                        <h3><?php echo $num_rows; ?></h3>
                        <p>Total Applications Received</p>
                    </span>
                    </a>
                </li>
            </ul>




            <div class="table-data">
                <div class="donut-container">
                    <canvas id="applicationStatusChart"></canvas>
                </div>

                <!-- Scholarship Analytics table -->
                <div class="scholarship-analytics">
                    <div class="head">
                        <h3>Scholarship Analytics</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Scholarship Name</th>
                                <th>Number of Applicants</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL query to retrieve scholarship names and count of applicants
                            $sql = "SELECT s.scholarship, COUNT(ua.user_id) AS num_applicants
                            FROM tbl_scholarship s
                            LEFT JOIN tbl_userapp ua ON s.scholarship_id = ua.scholarship_id
                            GROUP BY s.scholarship";

                            $listResult = mysqli_query($conn, $sql);

                            if ($listResult) {
                                while ($row = mysqli_fetch_assoc($listResult)) {
                                    echo '<tr>';
                                    echo '<td>' . $row['scholarship'] . '</td>';
                                    echo '<td class = "applicants-count"> <span class="num_applicants">'  . $row['num_applicants'] . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="2">Error executing the query: ' . mysqli_error($conn) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <?php
            function formatDateSubmitted($dbDateSubmitted)
            {
                $dateTimeObject = new DateTime($dbDateSubmitted);
                return $dateTimeObject->format('F d, Y');
            }
            ?>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Recent Applicants</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            while ($row = mysqli_fetch_array($select)) {
                                $statusClass = '';
                                switch ($row['status']) {
                                    case 'Pending':
                                        $statusClass = 'pending';
                                        break;
                                    case 'In Review':
                                        $statusClass = 'inreview';
                                        break;
                                    case 'Incomplete':
                                        $statusClass = 'incomplete';
                                        break;
                                    case 'Qualified':
                                        $statusClass = 'qualified';
                                        break;
                                    case 'Accepted':
                                        $statusClass = 'accepted';
                                        break;
                                    case 'Rejected':
                                        $statusClass = 'rejected';
                                        break;
                                    default:
                                        break;
                                }
                                echo '
                            <tr>
                                <td><img src="../user_profiles/' . $row['image'] . '" alt="">' . $row['applicant_name'] . '</td>
                                <td>' . formatDateSubmitted($row['date_submitted']) . '</td>
                                <td><p class="status ' . $statusClass . '">' . $row['status'] . '</td>
                            </tr>
                                ';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $newScholarsQuery = "SELECT * FROM tbl_userapp WHERE status = 'Qualified' ORDER BY application_id DESC LIMIT 10";
                $result = $conn->query($newScholarsQuery);
                ?>
                <div class="todo">
                    <div class="head">
                        <h3>New Scholars</h3>
                    </div>
                    <ul class="scholars_list">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<li class="scholar_container"><img class="scholar_image" src="../user_profiles/' . $row['image'] . '" alt=""> <span class="scholar_name">' . $row['applicant_name'] . ' </span> </li>';
                            }
                        } else {
                            echo '<li>No new scholars found.</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const statusCounts = {
            'Pending': <?php echo $pendingCount; ?>,
            'In Review': <?php echo $inReviewCount; ?>,
            'Qualified': <?php echo $qualifiedCount; ?>,
            'Accepted': <?php echo $acceptedCount; ?>,
            'Rejected': <?php echo $rejectedCount; ?>,
        };

        const ctx = document.getElementById('applicationStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    data: Object.values(statusCounts),
                    backgroundColor: [
                        '#fd7238', // Pending
                        '#ffce26', // In Review
                        '#00d084', // Qualified
                        '#28a745', // Accepted
                        'red', // Rejected
                    ],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true, 
                        text: 'Application Statistics', 
                        fontSize: 16, 
                    },
                },
            },
        });

        $(document).ready(function() {

            function confirmLogout() {
                Swal.fire({
                    title: "Logout",
                    text: "Are you sure you want to log out?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, log out",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "admin_logout.php";
                    }
                });
            }

            document.querySelector(".logout").addEventListener("click", function(event) {
                event.preventDefault(); 
                confirmLogout();
            });

             // TOGGLE SIDEBAR
        const menuBar = document.querySelector('#content nav .bx.bx-menu');
        const sidebar = document.getElementById('sidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('hide');
        }

        menuBar.addEventListener('click', toggleSidebar);

        function handleResize() {
            const screenWidth = window.innerWidth;

            if (screenWidth <= 768) {
                sidebar.classList.add('hide');
            } else {
                sidebar.classList.remove('hide');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();

            function toggleDropdown() {
                $(".num").hide();
            }

            $(".notification .bxs-bell").on("click", function(event) {
                event.stopPropagation();
                $(".dropdown").toggleClass("active");
                toggleDropdown();
                if ($(".dropdown").hasClass("active")) {
                    markAllNotificationsAsRead();
                } else {
                }
            });

            $(document).on("click", function() {
                $(".dropdown").removeClass("active");
            });

            function markAllNotificationsAsRead() {
                $.ajax({
                    url: "mark_notification_as_read.php", 
                    type: "POST",
                    data: {
                        read_message: "all" 
                    },
                    success: function() {
                        $(".notify_item").removeClass("unread");
                        fetchNotificationCount();
                    },
                    error: function() {
                        alert("Failed to mark notifications as read.");
                    }
                });
            }

            $(".notify_item").on("click", function() {
                var notificationId = $(this).data("notification-id");
                markNotificationAsRead(notificationId);
            });

            $(".notify_options .delete_option").on("click", function(event) {
                event.stopPropagation();
                const notificationId = $(this).data("notification-id");
                
                $.ajax({
                    url: "delete_notification.php", 
                    type: "POST",
                    data: {
                        notification_id: notificationId
                    },
                    success: function() {
                        $(".notify_item[data-notification-id='" + notificationId + "']").remove();
                        fetchNotificationCount();
                    },
                    error: function() {
                    }
                });
            });

            $(".notify_options .cancel_option").on("click", function(event) {
                event.stopPropagation();
                $(this).closest(".options_menu").removeClass("active");
            });
        });
    </script>

</body>

</html>