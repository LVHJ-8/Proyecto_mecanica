<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header("Location: ../Frontend/login.php"); 
    exit; 
}

// CORREGIDO: Uso de __DIR__ relativo para evitar las fallas que genera $_SERVER['DOCUMENT_ROOT'] en localhost:3000
require_once __DIR__ . '/../../adisionales/conexion.php';

try {
    // Consultas para llenar los selects y la tabla
    $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // NUEVO: Cargar los modelos dinámicamente desde la base de datos
    $modelos = $pdo->query("SELECT * FROM modelos_vehiculos ORDER BY nombre_modelo ASC")->fetchAll(PDO::FETCH_ASSOC);
    
    // ACTUALIZADO: LEFT JOIN agregado para traer la información del modelo asignado al producto
    $query_productos = "SELECT p.*, c.nombre as categoria, m.nombre as marca, mo.nombre_modelo as modelo 
                        FROM productos p 
                        LEFT JOIN categorias c ON p.categoria_id = c.id 
                        LEFT JOIN marcas m ON p.marca_id = m.id 
                        LEFT JOIN modelos_vehiculos mo ON p.modelo_id = mo.id 
                        ORDER BY p.id DESC";
    $productos = $pdo->query($query_productos)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>Gestión de Productos | LUVEN</title>
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
                <li class="active"><a href="../productos/CRUDProductos.php"><i class="fas fa-box"></i> <span>Productos</span></a></li>
                <li><a href="../servisios/CRUDServicios.php"><i class="fas fa-tools"></i> <span>Servicios</span></a></li>
                <li><a href="../citas/ControlCitas.php"><i class="fas fa-calendar-alt"></i> <span>Citas</span></a></li>
                <li><a href="../categoria/CRUDCategorias.php"><i class="fas fa-list"></i> <span>Categorías</span></a></li>
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
            <h1 class="page-title">Gestión de Productos</h1>
            
            <div class="tabs-header">
                <button class="tab-btn active" onclick="abrirPestana(event, 'tab-lista')"><i class="fas fa-list"></i> Lista de Productos</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-nuevo')"><i class="fas fa-plus-circle"></i> Nuevo Producto</button>
                <button class="tab-btn" onclick="abrirPestana(event, 'tab-preview')"><i class="fas fa-eye"></i> Previsualización</button>
            </div>

            <div id="tab-lista" class="tab-content active">
                <div class="dashboard-card">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Modelo</th> <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['id']); ?></td>
                                <td>
                                    <?php if (!empty($p['imagen'])): ?>
                                        <img src="../../assets/img/productos/<?php echo htmlspecialchars($p['imagen']); ?>" 
                                            width="45" height="45" style="object-fit: contain; border-radius: 4px;" alt="Producto">
                                    <?php else: ?>
                                        <span style="color: #aaa; font-size: 0.85rem;"><i class="fas fa-box"></i> Sin foto</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($p['marca'] ?? 'N/A'); ?></td>
                                <td><span class="badge-modelo" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; color: #475569; border: 1px solid #e2e8f0;"><?php echo htmlspecialchars($p['modelo'] ?? 'Ninguno'); ?></span></td> <td><strong>S/. <?php echo number_format($p['precio'], 2); ?></strong></td>
                                <td>
                                    <?php if ($p['stock'] <= 3): ?>
                                        <span style="color: red; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($p['stock']); ?></span>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($p['stock']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editar_producto.php?id=<?php echo $p['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_producto.php?id=<?php echo $p['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-nuevo" class="tab-content">
                <div class="dashboard-card">
                    <h3>Registrar Nuevo Producto</h3>
                    <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" class="form-grid">
                        <input type="text" name="nombre" placeholder="Nombre del producto" required>
                        
                        <select name="categoria_id" required>
                            <option value="">Seleccionar Categoría</option>
                            <?php foreach($categorias as $c): ?> 
                                <option value="<?php echo htmlspecialchars($c['id']); ?>"><?php echo htmlspecialchars($c['nombre']); ?></option> 
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="marca_id">
                            <option value="">Seleccionar Marca (Opcional)</option>
                            <?php foreach($marcas as $m): ?> 
                                <option value="<?php echo htmlspecialchars($m['id']); ?>"><?php echo htmlspecialchars($m['nombre']); ?></option> 
                            <?php endforeach; ?>
                        </select>

                        <select name="modelo_id">
                            <option value="">Seleccionar Modelo de Auto (Opcional)</option>
                            <?php foreach($modelos as $mod): ?> 
                                <option value="<?php echo htmlspecialchars($mod['id']); ?>"><?php echo htmlspecialchars($mod['nombre_modelo']); ?></option> 
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="number" name="precio" placeholder="Precio (Ej: 150.00)" step="0.01" min="0" required>
                        <input type="number" name="stock" placeholder="Stock disponible" min="0" required>
                        
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 0.9rem; color: #555; font-weight: bold;">Imagen del producto:</label>
                            <input type="file" name="imagen" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn-guardar">Guardar Producto</button>
                    </form>
                </div>
            </div>

            <div id="tab-preview" class="tab-content">
                <div class="dashboard-card">
                    <h3>Vista Pública de Productos</h3>
                    <p style="margin-bottom: 20px; color: #666;">Así es como verán los clientes las tarjetas de productos en la tienda web principal.</p>
                    
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="border: 1px solid #e0e0e0; padding: 20px; width: 240px; text-align: center; border-radius: 8px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <div style="height: 120px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 6px; margin-bottom: 12px;">
                                <i class="fas fa-box-open" style="font-size: 45px; color: #ccc;"></i>
                            </div>
                            <h4 style="margin: 0 0 8px 0; color: #333; font-size: 1.1rem;">Aceite Sintético 5W-30</h4>
                            <span style="display: inline-block; background: #e8f8f5; color: #1abc9c; padding: 3px 8px; font-size: 0.75rem; border-radius: 20px; font-weight: bold; margin-bottom: 10px;">Lubricantes</span>
                            <p style="color: #2c3e50; font-weight: bold; font-size: 1.3rem; margin: 0 0 12px 0;">S/. 145.00</p>
                            <button style="padding: 10px 15px; background: #34495e; color: white; border: none; border-radius: 4px; width: 100%; font-weight: bold; cursor: default;"><i class="fas fa-shopping-cart"></i> Añadir al carrito</button>
                        </div>
                    </div>
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