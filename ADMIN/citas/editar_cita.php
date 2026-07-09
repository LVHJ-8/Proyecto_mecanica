<?php
// 1. Validar e iniciar la sesión de administrador de forma segura
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../Frontend/login.php"); 
    exit; 
}

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// Validar que el ID exista en la URL antes de continuar
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: CRUDCitas.php");
    exit;
}

$id = trim($_GET['id']);

// 2. Procesar el formulario cuando se envía por POST (Actualización)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    
    if (!empty($estado)) {
        try {
            $stmt = $pdo->prepare("UPDATE citas SET estado = ? WHERE id = ?");
            $stmt->execute([$estado, $id]);
            // Redirige pasando un mensaje de éxito
            header("Location: CRUDCitas.php?mensaje=actualizado");
            exit;
        } catch (PDOException $e) {
            die("Error al actualizar: " . $e->getMessage());
        }
    }
}

// 3. Consultar los datos actuales de la cita para pintar el formulario (Lectura)
try {
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE id = ?");
    $stmt->execute([$id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si la cita no existe en la BD, regresamos al CRUD
    if (!$cita) {
        header("Location: CRUDCitas.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Estado de Cita</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container" style="max-width: 500px; margin: 50px auto;">
        <div class="dashboard-card">
            <h2>Actualizar Cita #<?php echo htmlspecialchars($id); ?></h2>
            
            <form method="POST" class="form-grid">
                <label>Cambiar Estado:</label>
                <select name="estado" style="padding: 10px; margin-bottom: 20px; width: 100%; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="Pendiente" <?php echo ($cita['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Confirmado" <?php echo ($cita['estado'] === 'Confirmado') ? 'selected' : ''; ?>>Confirmado</option>
                    <option value="Completado" <?php echo ($cita['estado'] === 'Completado') ? 'selected' : ''; ?>>Completado</option>
                    <option value="Cancelado" <?php echo ($cita['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                </select>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-guardar" style="flex: 1;">Guardar Cambios</button>
                    <a href="CRUDCitas.php" class="btn-accion" style="background: #7f8c8d; color: white; text-align: center; padding: 10px; border-radius: 4px; text-decoration: none; flex: 1;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>