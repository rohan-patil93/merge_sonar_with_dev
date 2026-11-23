<?php
$file = file('model/employee_loan.user.class.php');
echo "=== Check immediate returns ===\n";
foreach ($file as $idx => $line) {
    if (preg_match('/^\s*return\s+(\$[a-zA-Z0-9_]+);/', $line, $m)) {
        $prev = $idx > 0 ? trim($file[$idx - 1]) : '';
        if (strpos($prev, $m[1] . ' = ') !== false || strpos($prev, $m[1] . '=') !== false) {
            echo "Line " . ($idx + 1) . ": return {$m[1]} (prev: " . substr($prev, 0, 80) . ")\n";
        }
    }
}

echo "\n=== Check count() == 0 or > 0 ===\n";
foreach ($file as $idx => $line) {
    if (preg_match('/count\s*\([^)]+\)\s*==\s*0/', $line) || preg_match('/count\s*\([^)]+\)\s*<=\s*0/', $line) || preg_match('/count\s*\([^)]+\)\s*>\s*0/', $line)) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}

echo "\n=== Check lowercase NULL/FALSE/TRUE ===\n";
foreach ($file as $idx => $line) {
    if (preg_match('/\b(NULL|FALSE|TRUE)\b/', $line) && !preg_match('/^\s*(\*|\/\/)/', $line)) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}

echo "\n=== Check nested if statements ===\n";
for ($i = 0; $i < count($file) - 4; $i++) {
    $l1 = trim($file[$i]);
    $l2 = trim($file[$i + 1]);
    if (preg_match('/^if\s*\(.+\)\s*\{?$/', $l1) && preg_match('/^if\s*\(.+\)\s*\{?$/', $l2)) {
        echo "Line " . ($i + 1) . " & " . ($i + 2) . ":\n  $l1\n  $l2\n";
    }
}
