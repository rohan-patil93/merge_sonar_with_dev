<?php
$lines = file('model/crm_user.class.php');
for ($i = 732; $i <= 1210; $i++) {
    $l = trim($lines[$i]);
    if ($l === '' || strpos($l, '//') === 0 || strpos($l, '/*') === 0 || strpos($l, '*/') === 0 || strpos($l, '*') === 0) {
        continue;
    }
    echo "Line " . ($i + 1) . ": $l\n";
}
