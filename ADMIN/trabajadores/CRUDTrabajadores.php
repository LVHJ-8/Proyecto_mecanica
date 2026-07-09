<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { header("Location: ../Frontend/login.php"); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

// =========================================================================
// 1. PROCESAMIENTO DE ACCIONES (Auto-procesado en el mismo archivo)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // A. Guardar Nuevo Trabajador
    if ($_POST['action'] === 'guardar_trabajador') {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $cargo_id = $_POST['cargo_id'];
        $activo = 1; // Por defecto activo

        try {
            $stmt = $pdo->prepare("INSERT INTO trabajadores (cargo_id, nombre, apellido, telefono, dni, activo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cargo_id, $nombre, $apellido, $telefono, $dni, $activo]);
            header("Location: CRUDTrabajadores.php?status=success");
            exit;
        } catch (PDOException $e) {
            die("Error al guardar trabajador: " . $e->getMessage());
        }
    }

    // B. Guardar Nuevo Cargo (Desde la Sub-Ventana Modal)
    if ($_POST['action'] === 'guardar_cargo') {
        $nombre_cargo = $_POST['nombre_cargo'];
        $descripcion = $_POST['descripcion'];

        try {
            $stmt = $pdo->prepare("INSERT INTO cargos (nombre_cargo, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre_cargo, $descripcion]);
            header("Location: CRUDTrabajadores.php?status=cargo_ok");
            exit;
        } catch (PDOException $e) {
            die("Error al guardar el cargo: " . $e->getMessage());
        }
    }

    // C. Eliminar Cargo (Desde la Sub-Ventana Modal)
    if ($_POST['action'] === 'eliminar_cargo') {
        $id_cargo = $_POST['id_cargo'];
        try {
            $stmt = $pdo->prepare("DELETE FROM cargos WHERE id = ?");
            $stmt->execute([$id_cargo]);
            header("Location: CRUDTrabajadores.php?status=cargo_deleted");
            exit;
        } catch (PDOException $e) {
            die("No se puede eliminar el cargo porque tiene trabajadores asignados.");
        }
    }
}

// =========================================================================
// 2. CONSULTAS A LA BASE DE DATOS (Alineado a tu esquema real)
// =========================================================================
try {
    // Traemos los trabajadores haciendo un JOIN con cargos para ver el nombre del cargo real
    $queryTrabajadores = "SELECT t.*, c.nombre_cargo 
                          FROM trabajadores t 
                          LEFT JOIN cargos c ON t.cargo_id = c.id 
                          ORDER BY t.id DESC";
    $trabajadores = $pdo->query($queryTrabajadores)->fetchAll(PDO::FETCH_ASSOC);

    // Traemos todos los cargos para los select y la subventana
    $cargos = $pdo->query("SELECT * FROM cargos ORDER BY nombre_cargo ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Error en la base de datos: " . $e->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Gestión de Trabajadores | LUVEN</title>
    <link rel="stylesheet" href="../../assets/css/estilos1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .modal-cargos {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0;
            width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);
            align-items: center; justify-content: center;
        }
        .modal-content {
            background-color: #fff; padding: 25px; border-radius: 10px;
            width: 90%; max-width: 650px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative; animation: fadeIn 0.3s ease;
        }
        .close-modal {
            position: absolute; top: 15px; right: 20px; font-size: 24px;
            cursor: pointer; color: #7f8c8d;
        }
        .close-modal:hover { color: #c0392b; }
        .grid-modal { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
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
                <li><a href="../categoria/CRUDCategorias.php"><i class="fas fa-list"></i> <span>Categorías</span></a></li>
                <li><a href="../marcas/CRUDMarcas.php"><i class="fas fa-tag"></i> <span>Marcas</span></a></li>
                <li><a href="../modelos_De_veiculos/CRUDModelos.php"><i class="fas fa-car"></i> Modelos</a></li>
                <li class="active"><a href="../trabajadores/CRUDTrabajadores.php"><i class="fas fa-user-tie"></i> <span>Trabajadores</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <button id="toggle-sidebar"><i class="fas fa-bars"></i></button>
            <a href="../../adisionales/auth.php?accion=logout" style="color:red;"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </header>

        <div class="dashboard-container">
            <h1 class="page-title">Gestión de Trabajadores</h1>

            <div class="tabs-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista-trabajadores')"><i class="fas fa-list"></i> Lista de Trabajadores</button>
                    <button class="tab-btn" onclick="abrirPestana(event, 'tab-nuevo-trabajador')"><i class="fas fa-plus-circle"></i> Nuevo Trabajador</button>
                </div>
                <button class="btn-guardar" onclick="toggleModal(true)" style="background-color: #2980b9; margin-bottom: 10px;">
                    <i class="fas fa-briefcase"></i> Gestionar Cargos
                </button>
            </div>

            <div id="tab-lista-trabajadores" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead><tr><th>ID</th><th>DNI</th><th>Nombre Completo</th><th>Cargo / Especialidad</th><th>Teléfono</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($trabajadores as $t): ?>
                            <tr>
                                <td><?php echo $t['id']; ?></td>
                                <td><?php echo $t['dni']; ?></td>
                                <td><?php echo $t['nombre'] . " " . $t['apellido']; ?></td>
                                <td><span class="badge-cargo" style="background:#e8f4f8; color:#2980b9; padding:4px 8px; border-radius:5px; font-weight:bold;"><?php echo $t['nombre_cargo'] ?? 'Sin Asignar'; ?></span></td>
                                <td><?php echo $t['telefono']; ?></td>
                                <td>
                                    <a href="editar_trabajador.php?id=<?php echo $t['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_trabajador.php?id=<?php echo $t['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar a este trabajador?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nuevo-trabajador" class="tab-content">
                <div class="dashboard-card">
                    <h3>Registrar Nuevo Trabajador</h3>
                    <form action="" method="POST" class="form-grid">
                        <input type="hidden" name="action" value="guardar_trabajador">
                        
                        <input type="text" name="nombre" placeholder="Nombres" required>
                        <input type="text" name="apellido" placeholder="Apellidos" required>
                        <input type="text" name="dni" placeholder="DNI" required maxlength="15">
                        <input type="text" name="telefono" placeholder="Teléfono" required>
                        
                        <select name="cargo_id" required>
                            <option value="">Seleccione el Cargo o Especialidad</option>
                            <?php foreach ($cargos as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['nombre_cargo']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div style="grid-column: 1 / -1; margin-top: 15px;">
                            <button type="submit" class="btn-guardar">Guardar Trabajador</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<div id="modalCargos" class="modal-cargos">
    <div class="modal-content">
        <span class="close-modal" onclick="toggleModal(false)">&times;</span>
        <h2 style="color: #2c3e50; margin-bottom: 20px;"><i class="fas fa-briefcase"></i> Administrar Cargos del Taller</h2>
        
        <form action="" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <input type="hidden" name="action" value="guardar_cargo">
            <h4 style="margin-top:0; color:#2980b9;">Agregar Nuevo Cargo</h4>
            <div class="grid-modal">
                <input type="text" name="nombre_cargo" placeholder="Ej: Electricista Automotriz" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <input type="text" name="descripcion" placeholder="Descripción breve" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>
            <button type="submit" class="btn-guardar" style="padding: 6px 15px; font-size: 0.9rem;">+ Agregar Cargo</button>
        </form>

        <h4 style="color:#2c3e50;">Cargos Registrados</h4>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px;">
            <table class="crud-table" style="margin:0; width:100%;">
                <thead>
                    <tr><th>Cargo</th><th>Descripción</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cargos as $cg): ?>
                    <tr>
                        <td style="font-weight:bold;"><?php echo $cg['nombre_cargo']; ?></td>
                        <td><?php echo $cg['descripcion']; ?></td>
                        <td>
                            <form action="" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este cargo?');" style="display:inline;">
                                <input type="hidden" name="action" value="eliminar_cargo">
                                <input type="hidden" name="id_cargo" value="<?php echo $cg['id']; ?>">
                                <button type="submit" style="background:none; border:none; color:#e74c3c; cursor:pointer;"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../../assets/js/sidebar.js"></script>
<script>
    // Control de Pestañas (Tabs)
    function abrirPestana(evt, idPestana) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(idPestana).classList.add('active');
        evt.currentTarget.classList.add('active');
    }

    // Mostrar / Ocultar la Sub-Ventana Modal de Cargos
    function toggleModal(show) {
        const modal = document.getElementById('modalCargos');
        modal.style.display = show ? 'flex' : 'none';
    }

    // Cerrar la subventana si se hace clic fuera del recuadro blanco
    window.onclick = function(event) {
        const modal = document.getElementById('modalCargos');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>