<?php
require_once '../connection.php';

// Set error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration
$logFile = __DIR__ . '/db_stats.json';
$htmlReport = __DIR__ . '/db_report.html';

try {
    // Tables to monitor
    $tables = [
        'tbl_notification',
        'tbl_uploaded_document',
        'tbl_document_tracking',
        'tbl_action_taken',
        'tbl_conversation',
        'tbl_document_type',
        'tbl_notification_archive',
        'tbl_uploaded_document_archive'
    ];
    
    $stats = [
        'timestamp' => date('Y-m-d H:i:s'),
        'tables' => []
    ];
    
    // Get database size
    $stmt = $pdo->query("SELECT table_schema AS 'Database', 
                         ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' 
                         FROM information_schema.TABLES 
                         WHERE table_schema = 'document-tracking-db' 
                         GROUP BY table_schema");
    $dbSize = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['database_size_mb'] = $dbSize['Size (MB)'] ?? 0;
    
    // Get table statistics
    foreach ($tables as $table) {
        // Check if table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->execute(['table' => $table]);
        if ($stmt->rowCount() == 0) {
            continue; // Skip if table doesn't exist
        }
        
        // Get record count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get table size
        $stmt = $pdo->query("SELECT 
                             ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb 
                             FROM information_schema.TABLES 
                             WHERE table_schema = 'document-tracking-db' 
                             AND table_name = '$table'");
        $size = $stmt->fetch(PDO::FETCH_ASSOC)['size_mb'];
        
        $stats['tables'][$table] = [
            'record_count' => $count,
            'size_mb' => $size
        ];
    }
    
    // Save stats to JSON file
    $history = [];
    if (file_exists($logFile)) {
        $history = json_decode(file_get_contents($logFile), true);
    }
    
    // Keep only the last 30 entries
    $history[] = $stats;
    if (count($history) > 30) {
        $history = array_slice($history, -30);
    }
    
    file_put_contents($logFile, json_encode($history, JSON_PRETTY_PRINT));
    
    // Generate HTML report
    $html = '<!DOCTYPE html>
<html>
<head>
    <title>Database Monitoring Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #009933; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #009933; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .chart-container { width: 100%; height: 300px; margin-bottom: 30px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Database Monitoring Report</h1>
    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    
    <h2>Current Database Size: ' . $stats['database_size_mb'] . ' MB</h2>
    
    <h2>Table Statistics</h2>
    <table>
        <tr>
            <th>Table Name</th>
            <th>Record Count</th>
            <th>Size (MB)</th>
        </tr>';
    
    foreach ($stats['tables'] as $table => $data) {
        $html .= '<tr>
            <td>' . $table . '</td>
            <td>' . $data['record_count'] . '</td>
            <td>' . $data['size_mb'] . '</td>
        </tr>';
    }
    
    $html .= '</table>
    
    <h2>Database Size History</h2>
    <div class="chart-container">
        <canvas id="sizeChart"></canvas>
    </div>
    
    <h2>Record Count History</h2>
    <div class="chart-container">
        <canvas id="recordChart"></canvas>
    </div>
    
    <script>
        // Prepare data for charts
        const historyData = ' . json_encode($history) . ';
        const labels = historyData.map(entry => entry.timestamp);
        
        // Database size chart
        new Chart(document.getElementById("sizeChart"), {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Database Size (MB)",
                    data: historyData.map(entry => entry.database_size_mb),
                    borderColor: "#009933",
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Record count charts for main tables
        const mainTables = ["tbl_notification", "tbl_uploaded_document"];
        const datasets = mainTables.map(table => {
            return {
                label: table,
                data: historyData.map(entry => entry.tables[table]?.record_count || 0),
                borderColor: table.includes("notification") ? "#3366ff" : "#ff6633",
                tension: 0.1
            };
        });
        
        new Chart(document.getElementById("recordChart"), {
            type: "line",
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>';
    
    file_put_contents($htmlReport, $html);
    
    echo "Database monitoring completed successfully!\n";
    echo "Stats saved to: $logFile\n";
    echo "HTML report generated: $htmlReport\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}