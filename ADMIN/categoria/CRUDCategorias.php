<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../Frontend/login.php"); exit; }
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

try {
    $categorias = $pdo->query("SELECT * FROM categorias ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png"> 
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
                <li><a href="../servicios/CRUDServicios.php"><i class="fas fa-tools"></i> <span>Servicios</span></a></li>
                <li><a href="../citas/CRUDCitas.php"><i class="fas fa-calendar-alt"></i> <span>Citas</span></a></li>
                <li class="active"><a href="../categoria/CRUDCategorias.php"><i class="fas fa-list"></i> <span>Categorías</span></a></li>
                <li><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Marcas</span></a></li>
                <li><a href="../modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> <span>Modelos</span></a></li>
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
            <h1 class="page-title">Gestión de Categorías</h1>
            
            <div class="tabs-header">
                <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista-categorias')"><i class="fas fa-list"></i> Lista de Categorías</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-nueva-categoria')"><i class="fas fa-plus-circle"></i> Nueva Categoría</button>
            </div>

            <div id="tab-lista-categorias" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><?php echo $cat['nombre']; ?></td>
                                <td><?php echo $cat['descripcion']; ?></td>
                                <td>
                                    <a href="editar_categoria.php?id=<?php echo $cat['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_categoria.php?id=<?php echo $cat['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nueva-categoria" class="tab-content">
                <div class="dashboard-card">
                    <h3>Nueva Categoría</h3>
                    <form action="procesar_categoria.php" method="POST" class="form-grid">
                        <input type="text" name="nombre" placeholder="Nombre de la categoría" required>
                        <textarea name="descripcion" placeholder="Descripción de la categoría" rows="4"></textarea>
                        
                        <div style="grid-column: 1 / -1;">
                            <button type="submit" class="btn-guardar">Guardar Categoría</button>
                        </div>
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
</html>