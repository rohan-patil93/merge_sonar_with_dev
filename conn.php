<?php
/**
 * Database Connection Diagnostic Script
 * Tests whether the database is reachable, credentials are valid,
 * and queries can be executed.
 */

// Set fallbacks for CLI execution or direct access
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SESSION)) {
    $_SESSION = array();
}
if (!isset($_SESSION['userid'])) {
    $_SESSION['userid'] = 0;
}

$isCli = (php_sapi_name() === 'cli');

// Temporarily suppress warnings from duplicate constant definitions in dbconfig.php
$origErrorReporting = error_reporting();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Include main database configuration
$configFile = __DIR__ . '/dbconfig.php';
if (!file_exists($configFile)) {
    $msg = "Error: dbconfig.php not found at: " . $configFile;
    if ($isCli) {
        echo $msg . PHP_EOL;
    } else {
        echo "<h3 style='color:red;'>" . htmlspecialchars($msg) . "</h3>";
    }
    exit(1);
}

require_once $configFile;
error_reporting($origErrorReporting);

$host     = defined('HOSTNAME') ? HOSTNAME : 'localhost';
$username = defined('USERNAME') ? USERNAME : 'root';
$password = defined('PASSWORD') ? PASSWORD : '';
$database = defined('MAINDB') ? MAINDB : '';
$port     = defined('PORT') ? (int)PORT : 3306;

$status       = false;
$errorMessage = '';
$errorCode    = 0;
$serverInfo   = '';
$tableCount   = 0;
$queryTimeMs  = 0;
$testQueryOk  = false;

// Prevent PHP 8.1+ from throwing uncaught mysqli_sql_exception
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// Attempt MySQL connection
$startTime = microtime(true);
$conn = @mysqli_init();

if (!$conn) {
    $errorMessage = 'Failed to initialize mysqli';
    $errorCode = 1;
} else {
    // 5 seconds timeout
    @mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    try {
        $connected = @mysqli_real_connect($conn, $host, $username, $password, $database, $port);

        if ($connected) {
            $status = true;
            $serverInfo = @mysqli_get_server_info($conn);

            // Run a verification query to ensure database is responsive
            $qStart = microtime(true);
            $res = @mysqli_query($conn, "SELECT 1 AS test_ok");
            if ($res) {
                $testQueryOk = true;
                @mysqli_free_result($res);
            }
            $queryTimeMs = round((microtime(true) - $qStart) * 1000, 2);

            // Check table count if database is selected
            if (!empty($database)) {
                $tableRes = @mysqli_query($conn, "SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = '" . mysqli_real_escape_string($conn, $database) . "'");
                if ($tableRes && $row = @mysqli_fetch_assoc($tableRes)) {
                    $tableCount = (int)$row['total'];
                    @mysqli_free_result($tableRes);
                }
            }

            @mysqli_close($conn);
        } else {
            $errorCode    = mysqli_connect_errno();
            $errorMessage = mysqli_connect_error();
        }
    } catch (\Throwable $e) {
        $errorCode    = $e->getCode();
        $errorMessage = $e->getMessage();
    }
}

// Mask password for safe display
$maskedPassword = empty($password) ? '(empty)' : (strlen($password) > 4 ? substr($password, 0, 2) . str_repeat('*', strlen($password) - 4) . substr($password, -2) : '****');

// Output CLI format
if ($isCli) {
    echo "============================================" . PHP_EOL;
    echo "       DATABASE CONNECTION TEST RESULT      " . PHP_EOL;
    echo "============================================" . PHP_EOL;
    echo "Host:         " . $host . PHP_EOL;
    echo "Port:         " . $port . PHP_EOL;
    echo "User:         " . $username . PHP_EOL;
    echo "Password:     " . $maskedPassword . PHP_EOL;
    echo "Database:     " . $database . PHP_EOL;
    echo "--------------------------------------------" . PHP_EOL;
    if ($status) {
        echo "STATUS:       SUCCESS - CONNECTED & WORKING" . PHP_EOL;
        echo "MySQL Ver:    " . $serverInfo . PHP_EOL;
        echo "Test Query:   " . ($testQueryOk ? "PASSED ({$queryTimeMs} ms)" : "FAILED") . PHP_EOL;
        echo "Total Tables: " . $tableCount . PHP_EOL;
    } else {
        echo "STATUS:       FAILED TO CONNECT" . PHP_EOL;
        echo "Error Code:   " . $errorCode . PHP_EOL;
        echo "Error Msg:    " . $errorMessage . PHP_EOL;
    }
    echo "============================================" . PHP_EOL;
    exit($status ? 0 : 1);
}

// Output HTML format for browser viewing
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - InfiSuite</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 580px;
            overflow: hidden;
            border-top: 6px solid <?= $status ? '#28a745' : '#dc3545'; ?>;
        }
        .card-header {
            padding: 24px 28px 16px;
            border-bottom: 1px solid #edf2f7;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background: <?= $status ? '#e6f7ec' : '#fde8e8'; ?>;
            color: <?= $status ? '#1e7e34' : '#bd2130'; ?>;
            margin-bottom: 12px;
        }
        .status-badge .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: <?= $status ? '#28a745' : '#dc3545'; ?>;
        }
        h1 {
            font-size: 22px;
            color: #1a202c;
            margin-bottom: 6px;
        }
        p.subtitle {
            font-size: 14px;
            color: #718096;
        }
        .card-body {
            padding: 24px 28px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            color: #64748b;
            font-weight: 500;
        }
        .value {
            color: #0f172a;
            font-weight: 600;
            font-family: "Courier New", Courier, monospace;
        }
        .error-box {
            margin-top: 18px;
            padding: 14px 16px;
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            color: #991b1b;
            font-size: 13px;
        }
        .tips {
            margin-top: 14px;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
        .card-footer {
            padding: 16px 28px;
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-retry {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-retry:hover {
            background: #1d4ed8;
        }
        .timestamp {
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="status-badge">
                <span class="dot"></span>
                <?= $status ? 'Database Connected & Working' : 'Database Connection Failed'; ?>
            </div>
            <h1>Database Status</h1>
            <p class="subtitle">Checking configuration from <code>dbconfig.php</code></p>
        </div>
        <div class="card-body">
            <div class="detail-row">
                <span class="label">Host</span>
                <span class="value"><?= htmlspecialchars($host); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Port</span>
                <span class="value"><?= htmlspecialchars((string)$port); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Username</span>
                <span class="value"><?= htmlspecialchars($username); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Password</span>
                <span class="value"><?= htmlspecialchars($maskedPassword); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Database Name</span>
                <span class="value"><?= htmlspecialchars($database); ?></span>
            </div>

            <?php if ($status): ?>
                <div class="detail-row">
                    <span class="label">MySQL Version</span>
                    <span class="value"><?= htmlspecialchars($serverInfo); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Test Query</span>
                    <span class="value" style="color: #16a34a;">Passed (<?= $queryTimeMs; ?> ms)</span>
                </div>
                <div class="detail-row">
                    <span class="label">Total Tables in Database</span>
                    <span class="value"><?= $tableCount; ?></span>
                </div>
            <?php else: ?>
                <div class="error-box">
                    <strong>Error (<?= htmlspecialchars((string)$errorCode); ?>):</strong> <?= htmlspecialchars($errorMessage); ?>
                </div>
                <div class="tips">
                    <strong>Troubleshooting tips:</strong>
                    <ul style="padding-left: 18px; margin-top: 6px;">
                        <li>Verify MySQL service is running in XAMPP Control Panel.</li>
                        <li>Verify <code>PASSWORD</code> in <code>dbconfig.php</code> matches your MySQL root password (on local XAMPP it is often empty <code>""</code>).</li>
                        <li>Verify <code>MAINDB</code> (<code><?= htmlspecialchars($database); ?></code>) exists in your MySQL instance.</li>
                        <li>Ensure port <code><?= htmlspecialchars((string)$port); ?></code> is not blocked.</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <span class="timestamp">Tested at: <?= date('Y-m-d H:i:s'); ?></span>
            <a href="conn.php" class="btn-retry">↻ Test Again</a>
        </div>
    </div>
</body>
</html>
