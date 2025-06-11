<?php
session_start(); // Retained from 'main' for session management

$host = getenv('DB_HOST') ?: 'localhost'; // Prioritize environment variable, fallback to 'localhost'
$dbname = getenv('DB_NAME') ?: 'inventory'; // Prioritize environment variable, fallback to 'inventory'
$user = getenv('DB_USER') ?: 'root'; // Prioritize environment variable, fallback to 'root'
$pass = getenv('DB_PASS') ?: ''; // Prioritize environment variable, fallback to empty string

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>