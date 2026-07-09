<?php
// config/conexion.php

$host     = 'localhost';
$port     = '3307'; // Tu puerto específico
$db       = 'mecanica';
$user     = 'root';
$password = ''; // Coloca aquí tu contraseña si configuraste una en XAMPP
$charset  = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    // Creamos la conexión global $pdo
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}