<?php 
$host = "";
$dbname = 'askmecom_login';
$username = 'askmecom';
$password = 'ZO0r2YrisSVL';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo; // Ensure the PDO object is returned
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
