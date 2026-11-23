<?php
$file = 'model/employee_loan.user.class.php';
$lines = file($file);
$content = file_get_contents($file);

echo "Total lines: " . count($lines) . "\n";
echo "Has closing tag: " . (strpos($content, '?>') !== false ? "YES" : "NO") . "\n";
echo "EOF newline: " . (substr($content, -1) === "\n" ? "YES" : "NO") . "\n";

$tabs = 0;
foreach ($lines as $l) {
    if (strpos($l, "\t") !== false) $tabs++;
}
echo "Lines with tabs: $tabs\n";

$ws = 0;
foreach ($lines as $l) {
    if (preg_match('/[ \t]+$/', rtrim($l, "\r\n"))) $ws++;
}
echo "Lines with trailing whitespace: $ws\n";
