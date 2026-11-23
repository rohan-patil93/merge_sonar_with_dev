<?php
$lines = file('model/crm_user.class.php');
$open = false;
$start = 0;
foreach ($lines as $i => $l) {
    if (strpos($l, '/*') !== false) {
        echo "Found /* at line " . ($i + 1) . ": " . trim($l) . "\n";
    }
    if (strpos($l, '*/') !== false) {
        echo "Found */ at line " . ($i + 1) . ": " . trim($l) . "\n";
    }
}
