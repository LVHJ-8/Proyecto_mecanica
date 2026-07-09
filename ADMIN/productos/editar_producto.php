<?php
// 1. Validar e iniciar la sesión de administrador
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../../Frontend/login.php"); 
    exit; 
}

// 2. Incluir la conexión unificada a la base de datos
require_once __DIR__ . '/../../adisionales/conexion.php';

// Validar que exista un ID válido en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) { 
    header("Location: CRUDProductos.php"); 
    exit; 
}

$id = $_GET['id'];

try {
    // Obtener los datos actuales del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) { 
        die("Error: El producto no existe."); 
    }

    // Consultas para llenar los selectores (dropdowns)
    $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    // NUEVO: Cargar modelos para el selector
    $modelos = $pdo->query("SELECT * FROM modelos_vehiculos ORDER BY nombre_modelo ASC")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

// 3. Procesar la actualización cuando se envía el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $categoria_id = isset($_POST['categoria_id']) ? trim($_POST['categoria_id']) : '';
    $marca_id = !empty($_POST['marca_id']) ? trim($_POST['marca_id']) : null;
    $modelo_id = !empty($_POST['modelo_id']) ? trim($_POST['modelo_id']) : null; // NUEVO: Capturar modelo
    $precio = isset($_POST['precio']) ? trim($_POST['precio']) : 0;
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : 0;

    if (empty($nombre) || empty($categoria_id)) {
        die("Error: Nombre y Categoría son campos obligatorios.");
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($file_ext, $extensiones_permitidas)) {
            die("Error: Formato de imagen no permitido.");
        }

        $nombre_imagen = 'prod_' . uniqid() . '.' . $file_ext;
        $ruta_destino = __DIR__ . '/../../assets/img/productos/' . $nombre_imagen;

        if (!is_dir(dirname($ruta_destino))) {
            mkdir(dirname($ruta_destino), 0777, true);
        }

        if (move_uploaded_file($file_tmp, $ruta_destino)) {
            if (!empty($producto['imagen'])) {
                $ruta_imagen_vieja = __DIR__ . '/../../assets/img/productos/' . $producto['imagen'];
                if (file_exists($ruta_imagen_vieja)) {
                    unlink($ruta_imagen_vieja);
                }
            }
            
            // ACTUALIZADO: Incluir modelo_id en el SET y los parámetros
            $sql = "UPDATE productos SET nombre = ?, categoria_id = ?, marca_id = ?, modelo_id = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$nombre, $categoria_id, $marca_id, $modelo_id, $precio, $stock, $nombre_imagen, $id]);
        } else {
            die("Error al guardar la nueva imagen en el servidor.");
        }
    } else {
        // ACTUALIZADO: Incluir modelo_id en el SET y los parámetros (sin cambiar imagen)
        $sql = "UPDATE productos SET nombre = ?, categoria_id = ?, marca_id = ?, modelo_id = ?, precio = ?, stock = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$nombre, $categoria_id, $marca_id, $modelo_id, $precio, $stock, $id]);
    }

    header("Location: CRUDProductos.php?mensaje=actualizado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Editar Producto | LUVEN</title>
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
                <li class="active"><a href="CRUDProductos.php"><i class="fas fa-box"></i> <span>Volver a Productos</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Modificar Producto de Almacén</h1>

            <div class="dashboard-card" style="max-width: 650px; margin: 0 auto;">
                <h3>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h3>
                
                <form action="" method="POST" enctype="multipart/form-data" class="form-grid" style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
                    
                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Nombre del Producto:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required style="width: 100%; padding: 8px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Categoría:</label>
                        <select name="categoria_id" required style="width: 100%; padding: 8px;">
                            <?php foreach($categorias as $c): ?> 
                                <option value="<?php echo $c['id']; ?>" <?php echo ($producto['categoria_id'] == $c['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nombre']); ?>
                                </option> 
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Marca Asociada (Opcional):</label>
                        <select name="marca_id" style="width: 100%; padding: 8px;">
                            <option value="">Ninguna</option>
                            <?php foreach($marcas as $m): ?> 
                                <option value="<?php echo $m['id']; ?>" <?php echo ($producto['marca_id'] == $m['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m['nombre']); ?>
                                </option> 
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Modelo de Auto (Opcional):</label>
                        <select name="modelo_id" style="width: 100%; padding: 8px;">
                            <option value="">Ninguno</option>
                            <?php foreach($modelos as $mod): ?> 
                                <option value="<?php echo $mod['id']; ?>" <?php echo ($producto['modelo_id'] == $mod['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mod['nombre_modelo']); ?>
                                </option> 
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label style="font-weight: bold; display:block; margin-bottom:5px;">Precio (S/.):</label>
                            <input type="number" name="precio" step="0.01" min="0" value="<?php echo htmlspecialchars($producto['precio']); ?>" required style="width: 100%; padding: 8px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-weight: bold; display:block; margin-bottom:5px;">Stock Disponible:</label>
                            <input type="number" name="stock" min="0" value="<?php echo htmlspecialchars($producto['stock']); ?>" required style="width: 100%; padding: 8px;">
                        </div>
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Imagen de Exhibición Actual:</label>
                        <?php if (!empty($producto['imagen'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../../assets/img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" width="100" style="object-fit: contain; border: 1px solid #ddd; padding: 5px; border-radius: 4px;" alt="Producto actual">
                            </div>
                        <?php else: ?>
                            <p style="color: #999; font-size: 0.85rem;"><i class="fas fa-box-open"></i> Este producto no posee una imagen cargada.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label style="font-weight: bold; display:block; margin-bottom:5px;">Reemplazar Imagen (Opcional):</label>
                        <input type="file" name="imagen" accept="image/*">
                        <small style="color: #777; display:block; margin-top:3px;">No cargue archivos si desea mantener la imagen actual.</small>
                    </div>

                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="submit" class="btn-guardar" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px; flex: 1; font-weight: bold;">Actualizar Producto</button>
                        <a href="CRUDProductos.php" style="background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; text-align: center; flex: 1;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>