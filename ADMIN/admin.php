<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../Frontend/login.php");
    exit;
}

// Conexión
require_once($_SERVER['DOCUMENT_ROOT'] . '../adisionales/conexion.php');

try {
    // 1. Consultas para las tarjetas resumen
    $total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    $total_servicios = $pdo->query("SELECT COUNT(*) FROM servicios")->fetchColumn();
    $total_citas = $pdo->query("SELECT COUNT(*) FROM citas WHERE fecha = CURDATE()")->fetchColumn();

    // 2. Obtener productos con stock bajo (Muestra 10 o menos)
    $productos_bajos = $pdo->query("SELECT nombre, stock FROM productos WHERE stock <= 10 ORDER BY stock ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener próximas citas (🟢 MODIFICADO: Agregamos c.fecha a la selección)
    $query_citas = "
        SELECT c.fecha, COALESCE(h.formato_visible, c.hora) as hora, u.nombre as cliente 
        FROM citas c
        LEFT JOIN usuarios u ON c.usuario_id = u.id
        LEFT JOIN horarios_atencion h ON c.horario_id = h.id
        WHERE c.fecha >= CURDATE() 
        ORDER BY c.fecha ASC, c.hora ASC LIMIT 4";
    $proximas_citas = $pdo->query($query_citas)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Panel de Administración | LUVEN</title>
    <link rel="stylesheet" href="../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-cogs"></i> LUVEN Admin</div>
        <div class="sidebar-user">
            <img src="../assets/img/logos/logo de enpresa.png" alt="Avatar" class="user-avatar">
            <div><p class="user-welcome">Bienvenido,</p><p class="user-name">Administrador</p></div>
        </div>
        <nav class="sidebar-menu">
            <p class="menu-title">GENERAL</p>
            <ul>
                <li class="active"><a href="admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="productos/CRUDProductos.php"><i class="fas fa-box"></i> Productos</a></li>
                <li><a href="servisios/CRUDServicios.php"><i class="fas fa-tools"></i> Servicios</a></li>
                <li><a href="citas/CRUDCitas.php"><i class="fas fa-calendar-alt"></i> Citas</a></li>
                <li><a href="categorias/CRUDCategorias.php"><i class="fas fa-list"></i> Categorías</a></li>
                <li><a href="marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> Marcas</a></li>
                <li><a href="modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> Modelos</a></li>
                <li><a href="trabajadores/CRUDTrabajadores.php"><i class="fas fa-user-tie"></i> Trabajadores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Panel de Control</h1>

            <div class="form-grid">
                <div class="dashboard-card"><h3>Productos: <?php echo $total_productos; ?></h3></div>
                <div class="dashboard-card"><h3>Servicios: <?php echo $total_servicios; ?></h3></div>
                <div class="dashboard-card"><h3>Citas Hoy: <?php echo $total_citas; ?></h3></div>
            </div>

            <div class="form-grid">
                <div class="dashboard-card">
                    <div class="card-header"><h3>Stock Bajo (&le; 10)</h3></div>
                    <table class="crud-table">
                        <thead><tr><th>Producto</th><th>Stock</th></tr></thead>
                        <tbody>
                            <?php if (empty($productos_bajos)): ?>
                                <tr><td colspan="2" style="text-align: center; color: #888;">Todo el stock está al día.</td></tr>
                            <?php else: ?>
                                <?php foreach ($productos_bajos as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                    <td style="color: red; font-weight: bold;"><?php echo htmlspecialchars($p['stock']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="dashboard-card">
                    <div class="card-header"><h3>Próximas Citas</h3></div>
                    <table class="crud-table">
                        <thead><tr><th>Fecha</th><th>Hora</th><th>Cliente</th></tr></thead>
                        <tbody>
                            <?php if (empty($proximas_citas)): ?>
                                <tr><td colspan="3" style="text-align: center; color: #888;">No hay próximas citas agendadas.</td></tr>
                            <?php else: ?>
                                <?php foreach ($proximas_citas as $c): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($c['fecha'])); ?></td>
                                    <td><?php echo htmlspecialchars($c['hora']); ?></td>
                                    <td><?php echo htmlspecialchars($c['cliente'] ?? 'Cliente sin nombre'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>