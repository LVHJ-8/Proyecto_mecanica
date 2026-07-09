<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../Frontend/login.php"); exit; }

// se entra directo desde la raíz de htdocs
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';


try {
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Gestión de Marcas | LUVEN</title>
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
                <li class="active"><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Marcas</span></a></li>
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
            <h1 class="page-title">Gestión de Marcas</h1>

            <div class="tabs-header">
                <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista-marcas')"><i class="fas fa-list"></i> Lista de Marcas</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-nueva-marca')"><i class="fas fa-plus-circle"></i> Nueva Marca</button>
            </div>

            <div id="tab-lista-marcas" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead><tr><th>Logo</th><th>Nombre</th><th>Slug</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($marcas as $m): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($m['imagen'])): ?>
                                        <img src="../../assets/img/marcas/<?php echo htmlspecialchars($m['imagen']); ?>" 
                                             width="50" height="50" style="object-fit: contain; border-radius: 4px;" alt="logo">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.85rem;"><i class="fas fa-image"></i> Sin logo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($m['slug']); ?></td>
                                <td>
                                    <a href="editar_marca.php?id=<?php echo $m['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_marca.php?id=<?php echo $m['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta marca?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nueva-marca" class="tab-content">
                <div class="dashboard-card">
                    <h3>Nueva Marca</h3>
                    <form action="procesar_marca.php" method="POST" enctype="multipart/form-data" class="form-grid">
                        <input type="text" name="nombre" placeholder="Nombre de la marca" required>
                        <input type="text" name="slug" placeholder="Slug (ej: toyota)" required>
                        <input type="file" name="imagen" accept="image/*" required>
                        <button type="submit" class="btn-guardar">Guardar Marca</button>
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