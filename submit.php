<?php
$server = getenv("SQL_SERVER");
$database = getenv("SQL_DATABASE");

// Step 1: Get access token from Azure Instance Metadata Service
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://database.windows.net/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Metadata: true"));
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response)->access_token;

// Step 2: Convert token to UTF-8 binary (required by sqlsrv driver)
$accessToken = base64_decode($token);

// Step 3: Connect using sqlsrv_connect and access token
$connectionOptions = array(
    "Database" => $database,
    "Authentication" => SQLSRV_AUTH_ACTIVE_DIRECTORY_ACCESS_TOKEN,
    "AccessToken" => $accessToken
);

$conn = sqlsrv_connect($server, $connectionOptions);

if ($conn === false) {
    die("❌ Connection failed: " . print_r(sqlsrv_errors(), true));
}

// Step 4: Insert student
$fullname = $_POST['fullname'];
$email = $_POST['email'];

$sql = "INSERT INTO Students (FullName, Email) VALUES (?, ?)";
$params = array($fullname, $email);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("❌ Insert failed: " . print_r(sqlsrv_errors(), true));
} else {
    echo "✅ Student registered successfully!";
}

sqlsrv_close($conn);
?>

