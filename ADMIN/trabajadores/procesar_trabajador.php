<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../Frontend/login.php"); 
    exit; 
}

// 2. Incluir la conexión a la base de datos de manera dinámica
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// 3. Verificar que los datos vengan por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpiar y recibir los datos eliminando espacios vacíos extras
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $especialidad = isset($_POST['especialidad']) ? trim($_POST['especialidad']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';

    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre) || empty($especialidad) || empty($telefono)) {
        die("Error: Todos los campos son obligatorios.");
    }

    try {
        // 4. Preparar la consulta SQL de inserción
        $sql = "INSERT INTO trabajadores (nombre, especialidad, telefono) VALUES (:nombre, :especialidad, :telefono)";
        $stmt = $pdo->prepare($sql);

        // 5. Vincular los parámetros de forma segura
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':especialidad', $especialidad, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);

        // 6. Ejecutar la consulta
        if ($stmt->execute()) {
            // Si se guarda con éxito, redirige de vuelta a la lista de trabajadores
            header("Location: CRUDTrabajadores.php?mensaje=guardado");
            exit;
        } else {
            echo "Hubo un error al intentar registrar al trabajador.";
        }

    } catch (PDOException $e) {
        // En un entorno real es mejor no mostrar el $e->getMessage() directamente al usuario final por seguridad,
        // pero te servirá perfectamente durante el desarrollo.
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    // Si intentan entrar al archivo directamente por la URL sin enviar el formulario, los regresa al CRUD
    header("Location: CRUDTrabajadores.php");
    exit;
}
?>