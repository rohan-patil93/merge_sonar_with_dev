<?php
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
    $_SESSION['selected_application_languages'] = $_POST['selected_application_languages'];
    echo json_encode(['status' => 'success', 'message' => 'Application language updated successfully.']);
?>