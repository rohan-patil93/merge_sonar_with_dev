<?php
$lines = file('model/crm_user.class.php');
foreach ($lines as $idx => $line) {
    if (preg_match('/\b(AND|OR)\b/', $line)) {
        // check if this line is an SQL query or a PHP conditional
        if (preg_match('/^\s*if\s*\(/', $line) || preg_match('/^\s*while\s*\(/', $line) || preg_match('/^\s*\}\s*elseif\s*\(/', $line)) {
            echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
        }
    }
}
