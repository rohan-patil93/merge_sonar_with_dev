<?php
$file = 'model/crm_user.class.php';
$lines = file($file);

echo "=== Immediate Returns in $file ===\n";
foreach ($lines as $idx => $line) {
    if (preg_match('/^\s*return\s+(\$[a-zA-Z0-9_]+);/', $line, $m)) {
        $prev = $idx > 0 ? trim($lines[$idx - 1]) : '';
        if (strpos($prev, $m[1] . ' =') !== false || strpos($prev, $m[1] . '=') !== false) {
            echo "Line " . ($idx + 1) . ": return {$m[1]} | prev: $prev\n";
        }
    }
}

echo "\n=== Mergeable Nested Ifs in $file ===\n";
for ($i = 0; $i < count($lines) - 2; $i++) {
    $l1 = trim($lines[$i]);
    $l2 = trim($lines[$i + 1]);
    if (preg_match('/^if\s*\(.+\)\s*\{?$/', $l1) && preg_match('/^if\s*\(.+\)\s*\{?$/', $l2)) {
        echo "Line " . ($i + 1) . " & " . ($i + 2) . ":\n  $l1\n  $l2\n";
    }
}

echo "\n=== Empty checks (count(...) == 0) ===\n";
foreach ($lines as $idx => $line) {
    if (preg_match('/count\s*\([^)]+\)\s*(==|<=)\s*0/', $line) || preg_match('/count\s*\([^)]+\)\s*>\s*0/', $line)) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}

echo "\n=== Single line if without braces ===\n";
foreach ($lines as $idx => $line) {
    $t = trim($line);
    if (preg_match('/^if\s*\(.+?\)\s*[^\{;]+;$/', $t) && !preg_match('/\{/', $t)) {
        echo "Line " . ($idx + 1) . ": $t\n";
    }
}

echo "\n=== Uppercase TRUE/FALSE/NULL in PHP code ===\n";
foreach ($lines as $idx => $line) {
    $t = trim($line);
    if (preg_match('/\b(NULL|FALSE|TRUE)\b/', $t) && !preg_match('/^\s*(\*|\/\/)/', $t) && !preg_match('/(SELECT|WHERE|IS NULL|IS NOT NULL|INSERT|UPDATE)/i', $t)) {
        echo "Line " . ($idx + 1) . ": $t\n";
    }
}

echo "\n=== else if vs elseif in $file ===\n";
foreach ($lines as $idx => $line) {
    if (preg_match('/else\s+if/', $line)) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}
