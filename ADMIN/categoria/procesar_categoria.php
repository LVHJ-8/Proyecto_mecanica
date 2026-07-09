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

// 3. Verificar que los datos provengan del método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpiar entradas de texto para evitar espacios vacíos accidentales
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

    // El nombre es obligatorio según el atributo 'required' de tu formulario
    if (empty($nombre)) {
        die("Error: El nombre de la categoría es obligatorio.");
    }

    try {
        // 4. Preparar la consulta SQL para insertar en la tabla 'categorias'
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $pdo->prepare($sql);

        // 5. Vincular parámetros de manera segura
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

        // 6. Ejecutar y redirigir al panel de control
        if ($stmt->execute()) {
            header("Location: CRUDCategorias.php?mensaje=guardado");
            exit;
        } else {
            echo "Hubo un error interno al intentar registrar la categoría.";
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    // Si intentan forzar la entrada al script por URL, se les expulsa al CRUD
    header("Location: CRUDCategorias.php");
    exit;
}
?>