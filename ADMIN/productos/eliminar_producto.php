<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';
// Verificar si se recibió un ID por la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Preparar y ejecutar la consulta para eliminar
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Por si hay un error (ej. el producto está en una venta/cita)
        die("Error al eliminar el producto: " . $e->getMessage());
    }
}

// Redireccionar de vuelta a la página principal de productos
header("Location: CRUDProductos.php");
exit;
?>