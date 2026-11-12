<?php
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $_SESSION['selected_application_languages'] = $_POST['selected_application_languages'];
    echo json_encode(['status' => 'success', 'message' => 'Application language updated successfully.']);
?>