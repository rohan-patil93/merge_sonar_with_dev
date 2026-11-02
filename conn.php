<?php
@include('dbconfig.php');

try {
    $conn = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, MAINDB, PORT);
    if ($conn) {
        echo "Database Connected Successfully!";
    } else {
        echo "Database Connection Failed: " . mysqli_connect_error();
    }
} catch (Exception $e) {
    echo "Database Connection Failed: " . $e->getMessage();
}
?>
