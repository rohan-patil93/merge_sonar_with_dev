<?php
$file = $argv[1] ?? 'model/crm_user.class.php';
if (!file_exists($file)) {
    echo "File not found: $file\n";
    exit(1);
}
$lines = file($file);
$content = file_get_contents($file);

echo "File: $file\n";
echo "Total lines: " . count($lines) . "\n";
echo "Has closing tag: " . (strpos($content, '?>') !== false ? "YES" : "NO") . "\n";
echo "EOF newline: " . (substr($content, -1) === "\n" ? "YES" : "NO") . "\n";

$tabs = 0;
$trailing_ws = 0;
$bad_operators = 0;
$uppercase_consts = 0;
$mergeable_ifs = 0;

foreach ($lines as $i => $l) {
    if (strpos($l, "\t") !== false) $tabs++;
    if (preg_match('/[ \t]+$/', rtrim($l, "\r\n"))) $trailing_ws++;
    if (preg_match('/\s+(and|or)\s+/i', $l) && !preg_match('/(SELECT|WHERE|AND|OR|JOIN|FROM|INSERT|UPDATE|DELETE|ORDER BY|GROUP BY)/i', $l) && preg_match('/if\s*\(|while\s*\(/', $l)) {
        $bad_operators++;
    }
    if (preg_match('/\b(NULL|FALSE|TRUE)\b/', $l) && !preg_match('/^\s*(\*|\/\/)/', $l) && !preg_match('/(SELECT|WHERE|IS NULL|IS NOT NULL|INSERT|UPDATE)/i', $l)) {
        $uppercase_consts++;
    }
}

for ($i = 0; $i < count($lines) - 2; $i++) {
    $l1 = trim($lines[$i]);
    $l2 = trim($lines[$i + 1]);
    if (preg_match('/^if\s*\(.+\)\s*\{?$/', $l1) && preg_match('/^if\s*\(.+\)\s*\{?$/', $l2)) {
        $mergeable_ifs++;
    }
}

echo "Lines with tabs: $tabs\n";
echo "Lines with trailing whitespace: $trailing_ws\n";
echo "Lines with logical and/or: $bad_operators\n";
echo "Lines with uppercase TRUE/FALSE/NULL in PHP: $uppercase_consts\n";
echo "Mergeable nested ifs: $mergeable_ifs\n";
