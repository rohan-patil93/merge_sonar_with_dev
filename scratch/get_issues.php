<?php
$component = $argv[1] ?? 'infisuite-php-main-branch:model/employee_loan.user.class.php';
$url = "http://3.109.82.194:9000/api/issues/search?componentKeys=" . urlencode($component) . "&impactSoftwareQualities=MAINTAINABILITY&ps=500&statuses=OPEN,CONFIRMED";
$response = file_get_contents($url);
if (!$response) {
    echo "Failed to fetch from SonarQube\n";
    exit(1);
}
$data = json_decode($response, true);
echo "Total issues: " . ($data['total'] ?? 0) . "\n";

$by_rule = [];
foreach ($data['issues'] as $issue) {
    $rule = $issue['rule'];
    if (!isset($by_rule[$rule])) {
        $by_rule[$rule] = [];
    }
    $by_rule[$rule][] = [
        'key' => $issue['key'],
        'line' => $issue['line'] ?? null,
        'message' => $issue['message'] ?? '',
        'textRange' => $issue['textRange'] ?? null,
    ];
}

foreach ($by_rule as $rule => $issues) {
    echo "Rule {$rule}: " . count($issues) . " issues\n";
    foreach (array_slice($issues, 0, 5) as $iss) {
        echo "   Line " . ($iss['line'] ?? 'N/A') . ": " . $iss['message'] . "\n";
    }
}
