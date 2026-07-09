<?php
// 1. Conexión a la base de datos (asegúrate de incluirla)
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// 2. Validación de datos recibidos del formulario
if (empty($_POST['nombre']) || empty($_POST['precio'])) {
    die("Error: El nombre del servicio y el precio son obligatorios.");
}

$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$descripcion = $_POST['descripcion'] ?? ''; // Opcional

// 3. Validación numérica (usando el campo correcto 'precio')
if (!is_numeric($precio)) {
    die("Error: El precio debe ser un valor numérico válido.");
}

try {
    // 4. Inserción en la base de datos (usando la columna 'precio' de tu tabla)
    $sql = "INSERT INTO servicios (nombre, precio, descripcion) VALUES (:nombre, :precio, :descripcion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':precio' => $precio,
        ':descripcion' => $descripcion
    ]);

    // Redirigir al panel de administración tras el éxito
    header("Location: CRUDServicios.php?exito=1");
    exit;

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>