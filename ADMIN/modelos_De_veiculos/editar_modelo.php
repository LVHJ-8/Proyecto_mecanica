<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

// 2. Incluir conexión segura usando __DIR__
require_once __DIR__ . '/../../adisionales/conexion.php';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: CRUDModelos.php");
    exit;
}

$id = $_GET['id'];

// Obtener datos actuales del modelo
try {
    $stmt = $pdo->prepare("SELECT * FROM modelos_vehiculos WHERE id = ?");
    $stmt->execute([$id]);
    $mo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mo) { die("Error: El modelo no existe."); }

    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

// 3. Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_modelo = trim($_POST['nombre_modelo']);
    $marca_id = trim($_POST['marca_id']);
    
    // Definir directorio base (usando __DIR__ es infalible)
    $carpeta_destino = __DIR__ . '/../../assets/img/modelos/';
    
    // Crear carpeta si no existe
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_imagen = $mo['imagen_modelo']; // Mantener la anterior por defecto

    // Si se subió una nueva imagen
    if (isset($_FILES['imagen_modelo']) && $_FILES['imagen_modelo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen_modelo']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['imagen_modelo']['name'], PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $ext_permitidas)) {
            $nuevo_nombre = 'model_' . uniqid() . '.' . $file_ext;
            
            if (move_uploaded_file($file_tmp, $carpeta_destino . $nuevo_nombre)) {
                // Borrar vieja
                if (!empty($mo['imagen_modelo']) && file_exists($carpeta_destino . $mo['imagen_modelo'])) {
                    unlink($carpeta_destino . $mo['imagen_modelo']);
                }
                $nombre_imagen = $nuevo_nombre;
            } else {
                die("Error al subir la nueva imagen.");
            }
        }
    }

    // Actualizar base de datos
    $sql = "UPDATE modelos_vehiculos SET nombre_modelo = ?, marca_id = ?, imagen_modelo = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$nombre_modelo, $marca_id, $nombre_imagen, $id]);

    header("Location: CRUDModelos.php?mensaje=actualizado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Modelo | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-logo"><span>LUVEN Admin</span></div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="CRUDModelos.php"><i class="fas fa-arrow-left"></i> Volver a Modelos</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="dashboard-container">
                <h1 class="page-title">Modificar Modelo</h1>
                <div class="dashboard-card" style="max-width: 600px; margin: 0 auto;">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div>
                            <label>Nombre del Modelo:</label>
                            <input type="text" name="nombre_modelo" value="<?php echo htmlspecialchars($mo['nombre_modelo']); ?>" required style="width: 100%; padding: 8px;">
                        </div>

                        <div>
                            <label>Marca:</label>
                            <select name="marca_id" required style="width: 100%; padding: 8px;">
                                <?php foreach ($marcas as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo ($mo['marca_id'] == $m['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <p>Imagen Actual:</p>
                            <?php if (!empty($mo['imagen_modelo'])): ?>
                                <img src="../../assets/img/modelos/<?php echo htmlspecialchars($mo['imagen_modelo']); ?>" width="100">
                            <?php endif; ?>
                            <input type="file" name="imagen_modelo" accept="image/*">
                        </div>

                        <button type="submit" class="btn-guardar" style="margin-top: 15px;">Actualizar Modelo</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>