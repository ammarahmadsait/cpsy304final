<?php
// Load environment variables from Azure App Service Configuration
$server = getenv("SQL_SERVER");
$database = getenv("SQL_DATABASE");
$username = getenv("SQL_USERNAME");
$password = getenv("SQL_PASSWORD");

try {
    // Connect to Azure SQL Database using PDO
    $conn = new PDO("sqlsrv:server = $server; Database = $database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form values
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    // Insert into Students table
    $stmt = $conn->prepare("INSERT INTO Students (FullName, Email) VALUES (?, ?)");
    $stmt->execute([$fullname, $email]);

    echo "✅ Student registered successfully!";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
