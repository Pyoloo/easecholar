<?php
session_name("ApplicantSession");
session_start();

include '../include/connection.php';

$messageId = $_POST['message_id'];
$adminId = $_POST['admin_id'];
$registrarId = $_POST['registrar_id'];

if (!empty($adminId)) {
    // Mark admin message as read in tbl_user_messages
    $updateQuery = "UPDATE tbl_user_messages SET read_status = 'read' WHERE message_id = ?";
    $stmtUpdate = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmtUpdate, "i", $messageId);
    mysqli_stmt_execute($stmtUpdate);
} elseif (!empty($registrarId)) {
    // Mark registrar message as read in tbl_reg_messages
    $updateQuery = "UPDATE tbl_reg_messages SET read_status = 'read' WHERE message_id = ?";
    $stmtUpdate = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmtUpdate, "i", $messageId);
    mysqli_stmt_execute($stmtUpdate);
}

// Return a response indicating success
echo "Message marked as read.";
?>
