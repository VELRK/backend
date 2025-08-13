<?php
// Simple database check
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ferrigor';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection Test</h2>";
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
    
    // Check users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>Users in database: $userCount</p>";
    
    if ($userCount > 0) {
        $stmt = $pdo->query("SELECT id, name, username, email, status FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample Users:</h3>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>ID: {$user['id']}, Name: {$user['name']}, Username: {$user['username']}, Status: {$user['status']}</li>";
        }
        echo "</ul>";
    }
    
    // Check blogs table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM blogs");
    $blogCount = $stmt->fetch()['count'];
    echo "<p>Blogs in database: $blogCount</p>";
    
    if ($blogCount > 0) {
        $stmt = $pdo->query("SELECT id, title, user_id FROM blogs LIMIT 5");
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample Blogs:</h3>";
        echo "<ul>";
        foreach ($blogs as $blog) {
            echo "<li>ID: {$blog['id']}, Title: {$blog['title']}, User ID: {$blog['user_id']}</li>";
        }
        echo "</ul>";
    }
    
    echo "<hr>";
    echo "<h3>Test Links:</h3>";
    echo "<p><a href='http://localhost/ferrigor/users' target='_blank'>Users Page</a></p>";
    echo "<p><a href='http://localhost/ferrigor/blogs' target='_blank'>Blogs Page</a></p>";
    echo "<p><a href='http://localhost/ferrigor/test' target='_blank'>Test Controller</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
?> 