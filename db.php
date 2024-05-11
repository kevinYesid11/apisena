<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'apisena';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $e->getMessage()]));
}
