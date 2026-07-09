<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../../Frontend/login.php"); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// 1. OBTENER LOS DATOS DEL TRABAJADOR
if (!isset($_GET['id'])) { header("Location: CRUDTrabajadores.php"); exit; }
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM trabajadores WHERE id = ?");
$stmt->execute([$id]);
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) { die("Trabajador no encontrado."); }

// 2. PROCESAR LA ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $especialidad = $_POST['especialidad'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE trabajadores SET nombre = ?, especialidad = ?, telefono = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $especialidad, $telefono, $id]);

    header("Location: CRUDTrabajadores.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Trabajador | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
</head>
<body class="dashboard-body">
    <div class="dashboard-container" style="max-width: 800px; margin: 40px auto;">
        <div class="dashboard-card">
            <h2>Editar Trabajador: <?php echo htmlspecialchars($trabajador['nombre']); ?></h2>
            
            <form action="" method="POST" class="form-grid" style="margin-top: 20px;">
                <label>Nombre y Apellido</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($trabajador['nombre']); ?>" required>
                
                <label>Especialidad</label>
                <input type="text" name="especialidad" value="<?php echo htmlspecialchars($trabajador['especialidad']); ?>" required>
                
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($trabajador['telefono']); ?>" required>

                <div style="grid-column: 1 / -1; display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn-guardar" style="flex: 1;">Actualizar Trabajador</button>
                    <a href="CRUDTrabajadores.php" class="btn-accion" style="padding: 10px; background: #95a5a6; color: white; text-decoration: none; border-radius: 4px; text-align: center; flex: 1;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>