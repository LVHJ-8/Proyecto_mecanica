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
require_once __DIR__ . '/../../adisionales/conexion.php';

// 3. Verificar que los datos provengan del método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpiar texto de las entradas
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $categoria_id = isset($_POST['categoria_id']) ? trim($_POST['categoria_id']) : '';
    
    // Valores opcionales (si no se selecciona, se guardan como null)
    $marca_id = !empty($_POST['marca_id']) ? trim($_POST['marca_id']) : null;
    $modelo_id = !empty($_POST['modelo_id']) ? trim($_POST['modelo_id']) : null; // NUEVO: Captura de modelo
    
    $precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
    
    $nombre_imagen = null; 

    // Validar campos obligatorios
    if (empty($nombre) || empty($categoria_id) || empty($precio) || $stock === '') {
        die("Error: Todos los campos obligatorios deben estar llenos.");
    }

    // 4. Lógica de subida de imágenes
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $extensiones_permitidas)) {
            $nombre_imagen = 'prod_' . uniqid() . '.' . $file_ext;
            $ruta_destino = __DIR__ . '/../../assets/img/productos/' . $nombre_imagen;

            if (!is_dir(dirname($ruta_destino))) {
                mkdir(dirname($ruta_destino), 0777, true);
            }

            if (!move_uploaded_file($file_tmp, $ruta_destino)) {
                die("Error al subir la imagen al servidor.");
            }
        } else {
            die("Error: Formato de imagen no permitido.");
        }
    }

    try {
        // 5. Preparar la consulta SQL (Añadido modelo_id)
        $sql = "INSERT INTO productos (nombre, categoria_id, marca_id, modelo_id, precio, stock, imagen) 
                VALUES (:nombre, :categoria_id, :marca_id, :modelo_id, :precio, :stock, :imagen)";
        
        $stmt = $pdo->prepare($sql);

        // 6. Vincular parámetros
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindValue(':imagen', $nombre_imagen, $nombre_imagen === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        // Manejo de Marca
        if ($marca_id !== null) {
            $stmt->bindParam(':marca_id', $marca_id, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':marca_id', null, PDO::PARAM_NULL);
        }

        // Manejo de Modelo (NUEVO)
        if ($modelo_id !== null) {
            $stmt->bindParam(':modelo_id', $modelo_id, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':modelo_id', null, PDO::PARAM_NULL);
        }

        // 7. Ejecutar query
        if ($stmt->execute()) {
            header("Location: CRUDProductos.php?mensaje=guardado");
            exit;
        } else {
            echo "Error interno al intentar registrar el producto.";
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    header("Location: CRUDProductos.php");
    exit;
}
?>