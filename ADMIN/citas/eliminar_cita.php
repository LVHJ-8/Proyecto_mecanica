<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../Frontend/login.php"); 
    exit; 
}

// 2. Incluir la conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// 3. Verificar que el ID de la cita llegue a través de la URL (Método GET)
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    
    $cita_id = trim($_GET['id']);

    try {
        // 4. Preparar la consulta SQL de eliminación preventiva
        $sql = "DELETE FROM citas WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // 5. Vincular el parámetro ID de manera segura
        $stmt->bindParam(':id', $cita_id, PDO::PARAM_INT);

        // 6. Ejecutar la acción
        if ($stmt->execute()) {
            // Redirigir de vuelta al panel de control de citas con bandera de éxito
            header("Location: CRUDCitas.php?mensaje=eliminado");
            exit;
        } else {
            echo "No se pudo eliminar la cita seleccionada.";
        }

    } catch (PDOException $e) {
        die("Error al intentar eliminar el registro: " . $e->getMessage());
    }

} else {
    // Si no se envía un ID válido, regresar inmediatamente al CRUD
    header("Location: CRUDCitas.php");
    exit;
}
?>