<?php
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "portfolio_db";

$conn = new mysqli($host, $dbUsername, $dbPassword, $database);

if ($conn->connect_error) 
{
    die("Database connection failed: " . $conn->connect_error);
}
?>