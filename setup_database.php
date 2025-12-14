<?php
// setup_database.php
$host = 'localhost';
$username = 'root';
$password = ''; // Default XAMPP password

echo "<h1>Database Setup...</h1>";

try {
    // 1. Connect to MySQL Server (no DB selected yet)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✅ Connected to MySQL Server.</p>";

    // 2. Read SQL File
    $sql_file = __DIR__ . '/database.sql';
    if (!file_exists($sql_file)) {
        die("<p style='color:red'>❌ Error: database.sql not found.</p>");
    }
    $sql = file_get_contents($sql_file);

    // 3. Execute SQL
    $pdo->exec($sql);

    echo "<p style='color:green'>✅ Database `balatro_web` created successfully!</p>";
    echo "<p style='color:green'>✅ Tables created successfully!</p>";
    echo "<br><hr><br>";
    echo "<a href='login.php' style='font-size:2rem;'>GO TO LOGIN</a>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ CONNECTION FAILED: " . $e->getMessage() . "</p>";

    if (strpos($e->getMessage(), 'actively refused') !== false) {
        echo "<h2 style='color:red'>⚠️ YOUR MYSQL SERVER IS STOPPED ⚠️</h2>";
        echo "<p>Please open **XAMPP Control Panel** and click **START** next to **MySQL**.</p>";
        echo "<p>Then refresh this page.</p>";
    }
}
?>