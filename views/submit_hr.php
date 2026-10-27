<?php
$postdata = $_POST;
$url = API."?mode=employeedetailsupdate&method=post";
$response = file_post_contents($url, $_POST, 'insert');
$respons= json_decode($response,1);
