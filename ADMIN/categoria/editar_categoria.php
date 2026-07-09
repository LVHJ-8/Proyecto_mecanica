<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?")
        ->execute([$_POST['nombre'], $_POST['descripcion'], $id]);
    header("Location: CRUDCategorias.php");
    exit;
}

$cat = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$cat->execute([$id]);
$data = $cat->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container" style="max-width: 500px; margin: 50px auto;">
        <div class="dashboard-card">
            <h2>Editar Categoría</h2>
            <form method="POST" class="form-grid">
                <input type="text" name="nombre" value="<?php echo $data['nombre']; ?>" required>
                <textarea name="descripcion"><?php echo $data['descripcion']; ?></textarea>
                <button type="submit" class="btn-guardar">Actualizar</button>
            </form>
        </div>
    </div>
</body>
</html>