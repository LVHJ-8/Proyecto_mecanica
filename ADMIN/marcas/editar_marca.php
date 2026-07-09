<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

// 2. Incluir la conexión unificada
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// Validar que exista un ID válido en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: CRUDMarcas.php");
    exit;
}

$id = $_GET['id'];

// Obtener los datos actuales de la marca
$stmt = $pdo->prepare("SELECT * FROM marcas WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$m) {
    die("Error: La marca que intentas editar no existe.");
}

// 3. Procesar el formulario cuando se envía (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $slug = isset($_POST['slug']) ? strtolower(trim($_POST['slug'])) : '';
    
    if (empty($nombre) || empty($slug)) {
        die("Error: Todos los campos de texto son obligatorios.");
    }

    // 4. Si el usuario subió una NUEVA imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (!in_array($file_ext, $extensiones_permitidas)) {
            die("Error: Formato de archivo no permitido.");
        }

        $imgName = 'brand_' . uniqid() . '.' . $file_ext;

// 🟢 CORREGIDO: Usar __DIR__ para subir 2 niveles desde ADMIN/marcas/ hacia assets/
$ruta_destino = __DIR__ . '/../../assets/img/marcas/' . $imgName;

if (move_uploaded_file($file_tmp, $ruta_destino)) {
    
    // 🟢 OPTIMIZACIÓN: Borrar la imagen anterior del servidor para no dejar basura
    if (!empty($m['imagen'])) {
        // 🟢 CORREGIDO: Usar la misma ruta relativa segura para buscar la imagen vieja
        $ruta_imagen_vieja = __DIR__ . '/../../assets/img/marcas/' . $m['imagen'];
        
        if (file_exists($ruta_imagen_vieja)) {
            unlink($ruta_imagen_vieja);
        }
    }

            // Actualizar incluyendo la nueva imagen
            $sql = "UPDATE marcas SET nombre = ?, slug = ?, imagen = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$nombre, $slug, $imgName, $id]);
        } else {
            die("Error al guardar la nueva imagen en el servidor.");
        }
    } else {
        // 5. Si NO subió una nueva imagen, mantener la que ya tenía
        $sql = "UPDATE marcas SET nombre = ?, slug = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$nombre, $slug, $id]);
    }

    header("Location: CRUDMarcas.php?mensaje=actualizado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Editar Marca | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-cogs"></i> <span>LUVEN Admin</span></div>
        <nav class="sidebar-menu">
            <ul>
                <li><a href="../admin.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                <li class="active"><a href="CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Volver a Marcas</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Modificar Marca</h1>

            <div class="dashboard-card" style="max-width: 600px; margin: 0 auto;">
                <h3>Formulario de Edición</h3>
                
                <form action="" method="POST" enctype="multipart/form-data" class="form-grid" style="display: flex; flex-direction: column; gap: 15px;">
                    
                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Nombre de la marca:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($m['nombre']); ?>" required style="width: 100%; padding: 8px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Slug (ej: toyota):</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($m['slug']); ?>" required style="width: 100%; padding: 8px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Logotipo Actual:</label>
                        <?php if (!empty($m['imagen'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../../assets/img/marcas/<?php echo htmlspecialchars($m['imagen']); ?>" width="80" style="object-fit: contain; border: 1px solid #ddd; padding: 5px; border-radius: 4px;" alt="Logo actual">
                            </div>
                        <?php else: ?>
                            <p style="color: #999; font-size: 0.9rem;">No tiene ningún logotipo asignado actualmente.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Subir nuevo logotipo (Opcional):</label>
                        <input type="file" name="imagen" accept="image/*">
                        <small style="color: #666; display: block; margin-top: 2px;">Deje este campo vacío si desea conservar el logotipo actual.</small>
                    </div>

                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                        <button type="submit" class="btn-guardar" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px;">Actualizar Marca</button>
                        <a href="CRUDMarcas.php" style="background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; text-align: center;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>