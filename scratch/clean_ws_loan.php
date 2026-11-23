<?php
$file = 'model/employee_loan.user.class.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$newLines = [];
foreach ($lines as $line) {
    // replace tabs with 4 spaces
    $clean = str_replace("\t", "    ", $line);
    // remove trailing spaces and carriage returns
    $clean = rtrim($clean, " \t\r");
    $newLines[] = $clean;
}
$output = implode("\n", $newLines);
// ensure single trailing newline
$output = rtrim($output) . "\n";
file_put_contents($file, $output);
echo "Cleaned $file: " . count($newLines) . " lines.\n";
