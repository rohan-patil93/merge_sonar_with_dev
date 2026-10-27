<?php
  // Initialize a file URL to the variable
  $url = 'https://infisuite.com/beta/school/wp-content/uploads/elementor/forms/64240f28ecd09.docx';
  // Initialize the cURL session
  $ch = curl_init($url);
  // Initialize directory name where
  // file will be save
  $dir = 'uploads/all/';
  // Use basename() function to return
  // the base name of file
  $file_name = "file_".time()."_".uniqid()."_".basename($url);
  // Save file into file location
  $save_file_loc = $dir . $file_name;
  // Open file
  $fp = fopen($save_file_loc, 'wb');
  // It set an option for a cURL transfer
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  // Perform a cURL session
  curl_exec($ch);
  // Close file
  fclose($fp);
?>