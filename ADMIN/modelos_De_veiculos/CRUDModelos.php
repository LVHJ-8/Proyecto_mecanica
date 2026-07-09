<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../../Frontend/login.php"); exit; }

// 🟢 CORREGIDO: Ruta absoluta unificada con tu carpeta de entorno local
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

try {
    // Consulta para listar los modelos con el nombre de su marca respectiva
    $query = "SELECT m.id, m.nombre_modelo, m.imagen_modelo, ma.nombre as marca 
            FROM modelos_vehiculos m 
            JOIN marcas ma ON m.marca_id = ma.id 
            ORDER BY m.id DESC";
    $modelos = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Consulta para llenar el selector (dropdown) de marcas en el formulario
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Gestión de Modelos | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-cogs"></i> <span>LUVEN Admin</span></div>
        <div class="sidebar-user">
            <img src="../../assets/img/logos/logo de enpresa.png" alt="Avatar" class="user-avatar">
            <div><p class="user-welcome">Bienvenido,</p><p class="user-name">Administrador</p></div>
        </div>
        <nav class="sidebar-menu">
            <ul>
                <li><a href="../admin.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                <li><a href="../productos/CRUDProductos.php"><i class="fas fa-box"></i> <span>Productos</span></a></li>
                <li><a href="../servisios/CRUDServicios.php"><i class="fas fa-tools"></i> <span>Servicios</span></a></li>
                <li><a href="../citas/CRUDCitas.php"><i class="fas fa-calendar-alt"></i> <span>Citas</span></a></li>
                <li><a href="../categoria/CRUDCategorias.php"><i class="fas fa-list"></i> <span>Categorías</span></a></li>
                <li><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Marcas</span></a></li>
                <li class="active"><a href="../modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> <span>Modelos</span></a></li>
                <li><a href="../trabajadores/CRUDTrabajadores.php"><i class="fas fa-user-tie"></i> <span>Trabajadores</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Gestión de Modelos</h1>

            <div class="tabs-header">
                <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista-modelos')"><i class="fas fa-list"></i> Lista de Modelos</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-nuevo-modelo')"><i class="fas fa-plus-circle"></i> Nuevo Modelo</button>
            </div>

            <div id="tab-lista-modelos" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead><tr><th>Imagen</th><th>Modelo</th><th>Marca</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($modelos as $mo): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($mo['imagen_modelo'])): ?>
                                        <img src="../../assets/img/modelos/<?php echo htmlspecialchars($mo['imagen_modelo']); ?>" 
                                             width="50" height="50" style="object-fit: contain; border-radius: 4px;" alt="foto">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.85rem;"><i class="fas fa-image"></i> Sin foto</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($mo['nombre_modelo']); ?></td>
                                <td><?php echo htmlspecialchars($mo['marca']); ?></td>
                                <td>
                                    <a href="editar_modelo.php?id=<?php echo $mo['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_modelo.php?id=<?php echo $mo['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar este modelo?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nuevo-modelo" class="tab-content">
                <div class="dashboard-card">
                    <h3>Nuevo Modelo</h3>
                    <form action="procesar_modelo.php" method="POST" enctype="multipart/form-data" class="form-grid">
                        <input type="text" name="nombre_modelo" placeholder="Nombre del modelo" required>
                        
                        <select name="marca_id" required>
                            <option value="">Seleccione la Marca</option>
                            <?php foreach ($marcas as $ma): ?>
                                <option value="<?php echo htmlspecialchars($ma['id']); ?>"><?php echo htmlspecialchars($ma['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="file" name="imagen_modelo" accept="image/*" required>
                        <button type="submit" class="btn-guardar">Guardar Modelo</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../../assets/js/sidebar.js"></script>
<script>
    function abrirPestana(evt, idPestana) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(idPestana).classList.add('active');
        evt.currentTarget.classList.add('active');
    }
</script>
</body>
</html>-