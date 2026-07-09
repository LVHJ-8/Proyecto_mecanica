<?php
require_once "../adisionales/conexion.php";

$fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';

if (!empty($fecha)) {
    try {
        // Consultar las horas ya asignadas a esa fecha específica
        // NOTA: Ajusta los nombres de las columnas 'fecha' y 'hora' según tu tabla de base de datos
        $stmt = $pdo->prepare("SELECT hora_seleccionada FROM citas WHERE fecha_registro = ? AND estado != 'Cancelado'");
        $stmt->execute([$fecha]);
        
        // Extraer únicamente los valores de las horas en un array lineal
        $horasOcupadas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        // Retornar la lista en formato JSON para el JavaScript
        header('Content-Type: application/json');
        echo json_encode($horasOcupadas);
        exit;
    } catch (PDOException $e) {
        echo json_encode([]);
        exit;
    }
} else {
    echo json_encode([]);
    exit;
}
?>