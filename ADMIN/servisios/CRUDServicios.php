<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../Frontend/login.php"); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

try {
    $servicios = $pdo->query("SELECT * FROM servicios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Gestión de Servicios | LUVEN</title>
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
                <li class="active"><a href="../servicios/CRUDServicios.php"><i class="fas fa-tools"></i> <span>Servicios</span></a></li>
                <li><a href="../citas/CRUDCitas.php"><i class="fas fa-calendar-alt"></i> <span>Citas</span></a></li>
                <li><a href="../categoria/CRUDCategoria.php"><i class="fas fa-list"></i> <span>Categorías</span></a></li>
                <li><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Marcas</span></a></li>
                <li><a href="modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> Modelos</a></li>
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
            <h1 class="page-title">Gestión de Servicios</h1>

            <div class="tabs-header">
                <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista-servicios')"><i class="fas fa-list"></i> Lista de Servicios</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-nuevo-servicio')"><i class="fas fa-plus-circle"></i> Nuevo Servicio</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-preview-servicio')"><i class="fas fa-eye"></i> Previsualización</button>
            </div>

            <div id="tab-lista-servicios" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead><tr><th>ID</th><th>Nombre</th><th>Precio Base</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($servicios as $s): ?>
                            <tr>
                                <td><?php echo $s['id']; ?></td>
                                <td><?php echo $s['nombre']; ?></td>
                                <td>S/. <?php echo number_format($s['precio'], 2); ?></td>
                                <td>
                                    <a href="editar_servicio.php?id=<?php echo $s['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_servicio.php?id=<?php echo $s['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar este servicio?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nuevo-servicio" class="tab-content">
                <div class="dashboard-card">
                    <h3>Registrar Nuevo Servicio</h3>
                    <form action="procesar_servicio.php" method="POST" class="form-grid">
                        <input type="text" name="nombre" placeholder="Nombre del servicio" required>
                        <input type="number" name="precio" placeholder="Precio (Ej: 50.00)" step="0.01" required>
                        <textarea name="descripcion" placeholder="Descripción breve del servicio" class="full-width" style="grid-column: 1 / -1; min-height: 80px; padding: 10px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
                        <div style="grid-column: 1 / -1;">
                            <button type="submit" class="btn-guardar">Guardar Servicio</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-preview-servicio" class="tab-content">
                <div class="dashboard-card">
                    <h3>Vista Pública de Servicios</h3>
                    <p>Así es como verán los clientes los servicios ofrecidos en tu página principal.</p>
                    <div style="border: 1px solid #2980b9; padding: 20px; width: 280px; text-align: center; border-radius: 10px; margin-top: 15px; background-color: #f4f9f9; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <i class="fas fa-tools" style="font-size: 45px; color: #2980b9; margin-bottom: 15px;"></i>
                        <h4 style="color: #2c3e50; font-size: 1.2rem; margin-bottom: 10px;">Afinamiento de Motor</h4>
                        <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 15px;">Mantenimiento preventivo completo para asegurar el rendimiento óptimo de su vehículo.</p>
                        <p style="color: #2980b9; font-weight: bold; font-size: 1.3rem; margin-bottom: 15px;">S/. 120.00</p>
                        <button style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s;">Agendar Cita</button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../../assets/js/sidebar.js"></script>
<script>
    function abrirPestana(evt, idPestana) {
        var i, contenidos, botones;
        
        contenidos = document.getElementsByClassName("tab-content");
        for (i = 0; i < contenidos.length; i++) {
            contenidos[i].classList.remove("active");
        }
        
        botones = document.getElementsByClassName("tab-btn");
        for (i = 0; i < botones.length; i++) {
            botones[i].classList.remove("active");
        }
        
        document.getElementById(idPestana).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
</script>
</body>
</html>