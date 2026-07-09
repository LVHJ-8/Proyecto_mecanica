<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

// 2. Incluir la conexión usando la ruta relativa hacia tu carpeta "adisionales"
require_once __DIR__ . '/../../adisionales/conexion.php';

// 3. Verificar que los datos provengan del método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpiar entradas de texto para evitar espacios vacíos
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    
    // Convertir el slug a minúsculas y limpiar espacios (Evita textos largos de descripción)
    $slug = isset($_POST['slug']) ? strtolower(trim($_POST['slug'])) : '';

    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre) || empty($slug)) {
        die("Error: Todos los campos de texto son obligatorios.");
    }

    // 4. Validar y procesar el archivo del logo/imagen de la marca
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        die("Error: Debe seleccionar una imagen o logotipo válido para la marca.");
    }

    $file_tmp  = $_FILES['imagen']['tmp_name'];
    $file_name = $_FILES['imagen']['name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Extensiones permitidas para los logotipos de las marcas
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

    if (!in_array($file_ext, $extensiones_permitidas)) {
        die("Error: Formato de archivo no permitido. Use JPG, JPEG, PNG, WEBP o SVG.");
    }

    // Generar un nombre único e irrepetible para la imagen
    $nombre_imagen = 'brand_' . uniqid() . '.' . $file_ext;
    
    // 🟢 RUTA CORREGIDA: Sube 2 niveles al directorio raíz y entra directo a assets/img/marcas/
    $ruta_destino = __DIR__ . '/../../assets/img/marcas/' . $nombre_imagen;

    // Crear la carpeta físicamente si no existiera en el servidor
    if (!is_dir(dirname($ruta_destino))) {
        mkdir(dirname($ruta_destino), 0777, true);
    }

    // Mover el archivo temporal de la imagen a su carpeta final en el proyecto
    if (!move_uploaded_file($file_tmp, $ruta_destino)) {
        die("Error al guardar la imagen de la marca en el servidor.");
    }

    try {
        // 5. Preparar la consulta SQL limpia y segura con PDO
        $sql  = "INSERT INTO marcas (nombre, slug, imagen) VALUES (:nombre, :slug, :imagen)";
        $stmt = $pdo->prepare($sql);

        // 6. Vincular los parámetros de forma segura para evitar Inyección SQL
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindParam(':imagen', $nombre_imagen, PDO::PARAM_STR);

        // 7. Ejecutar la consulta en la Base de Datos
        if ($stmt->execute()) {
            // Redireccionar de vuelta al panel de marcas con mensaje de éxito
            header("Location: CRUDMarcas.php?mensaje=guardado");
            exit;
        } else {
            echo "Hubo un error interno al intentar registrar la marca.";
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    // Si intentan forzar la URL directamente en el navegador, los regresa al CRUD
    header("Location: CRUDMarcas.php");
    exit;
}
?>