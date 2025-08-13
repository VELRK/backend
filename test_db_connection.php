<?php
// Test database connection and email activities
require_once 'application/config/database.php';

try {
    // Create PDO connection
    $dsn = "mysql:host=" . $db['default']['hostname'] . ";dbname=" . $db['default']['database'] . ";charset=utf8";
    $pdo = new PDO($dsn, $db['default']['username'], $db['default']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection Test</h2>";
    echo "<p style='color: green;'>✓ Database connected successfully!</p>";
    
    // Test email_activities table
    $stmt = $pdo->query("SHOW TABLES LIKE 'email_activities'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ email_activities table exists!</p>";
        
        // Count records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM email_activities");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Total email activities: <strong>{$count}</strong></p>";
        
        // Show sample data
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM email_activities ORDER BY date DESC LIMIT 5");
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Sample Data (Last 5):</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Message</th><th>Full Name</th><th>Email</th><th>Date</th></tr>";
            
            foreach ($activities as $activity) {
                echo "<tr>";
                echo "<td>{$activity['id']}</td>";
                echo "<td>{$activity['message']}</td>";
                echo "<td>{$activity['fullname']}</td>";
                echo "<td>{$activity['email']}</td>";
                echo "<td>{$activity['date']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠ No data found in email_activities table</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ email_activities table does not exist!</p>";
        echo "<p>Please run the create_email_activities_table.sql script first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Connection Error</h2>";
    echo "<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>";
}
?> 