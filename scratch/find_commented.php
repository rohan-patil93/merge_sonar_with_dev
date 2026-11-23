<?php
$file = $argv[1] ?? 'model/crm_user.class.php';
$lines = file($file);
foreach ($lines as $i => $line) {
    $trimmed = trim($line);
    if (preg_match('#^//\s*(\$[a-zA-Z0-9_]+|return |if\s*\(|while|for|/\*|\$callConfig|echo |print)#', $trimmed)) {
        echo "Line " . ($i + 1) . ": $trimmed\n";
    }
}
