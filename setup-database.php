<?php
// Database setup script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Setup for Axumite Tours</h1>";

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'abeltesfay');

try {
    // Connect to MySQL without specifying database first
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("<p style='color: red;'>❌ Failed to connect to MySQL: " . $conn->connect_error . "</p>");
    }
    
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS axumite_tours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Database 'axumite_tours' created/verified</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating database: " . $conn->error . "</p>";
    }
    
    // Switch to the database
    $conn->select_db('axumite_tours');
    
    // Create bookings table
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id          INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(100)  NOT NULL,
        email       VARCHAR(150)  NOT NULL,
        phone       VARCHAR(30)   DEFAULT NULL,
        destination VARCHAR(100)  DEFAULT NULL,
        date        DATE          DEFAULT NULL,
        guests      INT           DEFAULT 1,
        message     TEXT          NOT NULL,
        created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✅ Bookings table created/verified</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
    }
    
    // Verify table exists and show structure
    $result = $conn->query("SHOW TABLES LIKE 'bookings'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table verification successful</p>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE bookings");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
        echo "<tr style='background: #f0f0f0;'><th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Null</th><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$row['Field']}</td>";
            echo "<td style='padding: 8px;'>{$row['Type']}</td>";
            echo "<td style='padding: 8px;'>{$row['Null']}</td>";
            echo "<td style='padding: 8px;'>{$row['Key']}</td>";
            echo "<td style='padding: 8px;'>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test insert
        echo "<h3>Testing Insert Operation:</h3>";
        $test_sql = "INSERT INTO bookings (name, email, phone, destination, date, guests, message) 
                    VALUES ('Test User', 'test@example.com', '1234567890', 'Lalibela', '2026-12-01', 2, 'Test booking message')";
        
        if ($conn->query($test_sql)) {
            echo "<p style='color: green;'>✅ Test insert successful! ID: " . $conn->insert_id . "</p>";
            
            // Clean up test entry
            $conn->query("DELETE FROM bookings WHERE email = 'test@example.com'");
            echo "<p style='color: blue;'>🧹 Test entry cleaned up</p>";
        } else {
            echo "<p style='color: red;'>❌ Test insert failed: " . $conn->error . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table was not created properly</p>";
    }
    
    $conn->close();
    
    echo "<h2>✅ Database setup complete!</h2>";
    echo "<p>Your booking system should now work properly.</p>";
    echo "<p><a href='test-booking.php'>Test the booking system</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ul>";
    echo "<li>Ensure MySQL/MariaDB is running</li>";
    echo "<li>Check database credentials (user: root, password: abeltesfay)</li>";
    echo "<li>Verify user has CREATE database privileges</li>";
    echo "<li>Make sure PHP MySQLi extension is enabled</li>";
    echo "</ul>";
}
?>
