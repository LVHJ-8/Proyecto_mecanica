<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM trabajadores WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("Error al eliminar el trabajador: " . $e->getMessage());
    }
}

header("Location: CRUDTrabajadores.php");
exit;
?>