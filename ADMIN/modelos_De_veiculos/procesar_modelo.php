<?php
// 1. Iniciar sesión y validar admin
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

// 2. Incluir conexión
require_once __DIR__ . '/../../adisionales/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_modelo = isset($_POST['nombre_modelo']) ? trim($_POST['nombre_modelo']) : '';
    $marca_id = isset($_POST['marca_id']) ? trim($_POST['marca_id']) : '';

    if (empty($nombre_modelo) || empty($marca_id)) {
        die("Error: El nombre del modelo y la marca son obligatorios.");
    }

    $nombre_imagen = null;

    // 3. Lógica de subida de imagen
    if (isset($_FILES['imagen_modelo']) && $_FILES['imagen_modelo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen_modelo']['tmp_name'];
        $file_name = $_FILES['imagen_modelo']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $extensiones_permitidas)) {
            // Nombre único
            $nombre_imagen = 'mod_' . uniqid() . '.' . $file_ext;
            $ruta_destino = __DIR__ . '/../../assets/img/modelos/' . $nombre_imagen;

            // Crear carpeta si no existe
            if (!is_dir(dirname($ruta_destino))) {
                mkdir(dirname($ruta_destino), 0777, true);
            }

            if (!move_uploaded_file($file_tmp, $ruta_destino)) {
                die("Error: No se pudo mover la imagen a la carpeta de destino.");
            }
        } else {
            die("Error: Formato de imagen no permitido.");
        }
    } else {
        die("Error: Debes subir una imagen para el modelo.");
    }

    // 4. Insertar en la base de datos
    try {
        $sql = "INSERT INTO modelos_vehiculos (nombre_modelo, marca_id, imagen_modelo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_modelo, $marca_id, $nombre_imagen]);

        header("Location: CRUDModelos.php?mensaje=guardado");
        exit;
    } catch (PDOException $e) {
        die("Error en base de datos: " . $e->getMessage());
    }
} else {
    header("Location: CRUDModelos.php");
    exit;
}
?>