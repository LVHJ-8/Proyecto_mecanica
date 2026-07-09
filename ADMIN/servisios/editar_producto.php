<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../../Frontend/login.php"); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '/Proyecto_mecanica/adisionales/conexion.php';

// 1. OBTENER LOS DATOS DEL SERVICIO
if (!isset($_GET['id'])) { header("Location: CRUDServicios.php"); exit; }
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$id]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) { die("Servicio no encontrado."); }

// 2. PROCESAR LA ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio']; // Cambiado de precio_base a precio
    $descripcion = $_POST['descripcion'];

    // Cambiado: precio_base = ? por precio = ?
    $sql = "UPDATE servicios SET nombre = ?, precio = ?, descripcion = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $precio, $descripcion, $id]);

    header("Location: CRUDServicios.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
</head>
<body class="dashboard-body">
    <div class="dashboard-container" style="max-width: 800px; margin: 40px auto;">
        <div class="dashboard-card">
            <h2>Editar Servicio: <?php echo htmlspecialchars($servicio['nombre']); ?></h2>
            
            <form action="" method="POST" class="form-grid" style="margin-top: 20px;">
                <label style="grid-column: 1 / -1;">Nombre del Servicio</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($servicio['nombre']); ?>" required style="grid-column: 1 / -1;">
                
                <label style="grid-column: 1 / -1;">Precio (S/.)</label>
                <input type="number" name="precio" step="0.01" value="<?php echo $servicio['precio']; ?>" required style="grid-column: 1 / -1;">
                
                <label style="grid-column: 1 / -1;">Descripción</label>
                <textarea name="descripcion" style="grid-column: 1 / -1; min-height: 100px; padding: 10px; border-radius: 4px; border: 1px solid #ccc;"><?php echo htmlspecialchars($servicio['descripcion'] ?? ''); ?></textarea>

                <div style="grid-column: 1 / -1; display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn-guardar" style="flex: 1;">Actualizar Servicio</button>
                    <a href="CRUDServicios.php" class="btn-accion" style="padding: 10px; background: #95a5a6; color: white; text-decoration: none; border-radius: 4px; text-align: center; flex: 1;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>