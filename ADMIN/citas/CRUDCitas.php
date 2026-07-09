<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../Frontend/login.php"); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

try {
    // 🟢 CÓDIGO CORREGIDO: Usamos LEFT JOIN para evitar que las citas se oculten
    // Y COALESCE para mostrar la hora de la tabla 'citas' si no encuentra relación en 'horarios_atencion'
    $query = "SELECT c.id, 
                     u.nombre as cliente, 
                     s.nombre as servicio, 
                     COALESCE(h.formato_visible, c.hora) as hora, 
                     c.fecha, 
                     c.estado 
              FROM citas c
              LEFT JOIN usuarios u ON c.usuario_id = u.id
              LEFT JOIN servicios s ON c.servicio_id = s.id
              LEFT JOIN horarios_atencion h ON c.horario_id = h.id
              ORDER BY c.fecha DESC, c.hora ASC";
              
    $citas = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Control de Citas | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-cogs"></i> LUVEN Admin</div>
        <div class="sidebar-user">
            <img src="../../assets/img/logos/logo de enpresa.png" alt="Avatar" class="user-avatar">
            <div><p class="user-welcome">Bienvenido,</p><p class="user-name">Administrador</p></div>
        </div>
        <nav class="sidebar-menu">
            <ul>
                <li><a href="../admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="../productos/CRUDProductos.php"><i class="fas fa-box"></i> Productos</a></li>
                <li><a href="../servisios/CRUDServicios.php"><i class="fas fa-tools"></i> Servicios</a></li>
                <li class="active"><a href="../citas/CRUDCitas.php"><i class="fas fa-calendar-alt"></i> Citas</a></li>
                <li><a href="../categoria/CRUDCategorias.php"><i class="fas fa-list"></i> Categorías</a></li>
                <li><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> Marcas</a></li>
                <li><a href="modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> Modelos</a></li>
                <li><a href="../trabajadores/CRUDTrabajadores.php"><i class="fas fa-user-tie"></i> Trabajadores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Control de Citas</h1>

            <div class="dashboard-card">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citas)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                                    No se encontraron citas programadas en la base de datos.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citas as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($c['hora']); ?></td>
                                <td><?php echo htmlspecialchars($c['cliente'] ?? 'Cliente no registrado'); ?></td>
                                <td><?php echo htmlspecialchars($c['servicio'] ?? 'Servicio no especificado'); ?></td>
                                <td>
                                    <span class="estado-badge <?php echo strtolower($c['estado']); ?>">
                                        <?php echo htmlspecialchars($c['estado']); ?>
                                    </span>
                                </td>
                                <td class="acciones-container">
                                    <a href="editar_cita.php?id=<?php echo $c['id']; ?>" class="btn-accion btn-editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="eliminar_cita.php?id=<?php echo $c['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar esta cita definitivamente?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>