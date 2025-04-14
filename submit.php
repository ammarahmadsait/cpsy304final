<?php
$server = getenv("SQL_SERVER");
$database = getenv("SQL_DATABASE");

// Get an access token from the Azure Instance Metadata Service
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://database.windows.net/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Metadata: true"));
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response)->access_token;

try {
    $conn = new PDO("sqlsrv:server=$server;Database=$database", "", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Attach token
    $conn->setAttribute(PDO::SQLSRV_ATTR_ACCESS_TOKEN, $token);

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
