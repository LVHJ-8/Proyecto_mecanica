<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta absoluta unificada para evitar fallos de carga en el servidor local
require_once __DIR__ . '/../adisionales/conexion.php';

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$cat_id = isset($_GET['categoria_id']) ? trim($_GET['categoria_id']) : '';
$marcaFiltrada = isset($_GET['marca']) ? trim($_GET['marca']) : '';
$modeloFiltrado = isset($_GET['modelo']) ? trim($_GET['modelo']) : ''; // Filtro por ID de modelo

try {
    // Cargar categorías para el selector y la barra lateral de navegación
    $cat_query = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $cat_query->fetchAll(PDO::FETCH_ASSOC);

    // Cargar marcas de manera dinámica desde la base de datos
    $marcas_query = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC");
    $marcas_dinamicas = $marcas_query->fetchAll(PDO::FETCH_ASSOC);

    // CORREGIDO: Tabla con "h" y ordenamiento por "nombre_modelo" real para evitar el error Column not found
    $modelos_query = $pdo->query("SELECT * FROM modelos_vehiculos ORDER BY nombre_modelo ASC");
    $modelos_dinamicos = $modelos_query->fetchAll(PDO::FETCH_ASSOC);

    // Consulta dinámica optimizada mediante LEFT JOIN para marcas y modelos_vehiculos
    $sql = "SELECT p.*, m.nombre as marca_nombre, mo.nombre_modelo as modelo_nombre 
            FROM productos p 
            LEFT JOIN marcas m ON p.marca_id = m.id 
            LEFT JOIN modelos_vehiculos mo ON p.modelo_id = mo.id 
            WHERE p.activo = 1";
    $params = [];

    if (!empty($buscar)) {
        $sql .= " AND (p.nombre LIKE :buscar OR p.descripcion LIKE :buscar)";
        $params[':buscar'] = '%' . $buscar . '%';
    }
    
    if (!empty($cat_id)) {
        $sql .= " AND p.categoria_id = :cat_id";
        $params[':cat_id'] = $cat_id;
    }
    
    if (!empty($marcaFiltrada)) {
        $sql .= " AND m.slug = :marca";
        $params[':marca'] = $marcaFiltrada;
    }

    // CORREGIDO: Filtro relacional usando el ID del modelo (p.modelo_id)
    if (!empty($modeloFiltrado)) {
        $sql .= " AND p.modelo_id = :modelo";
        $params[':modelo'] = $modeloFiltrado;
    }

    // Orden lógico: primero productos con stock disponible y luego los agotados
    $sql .= " ORDER BY CASE WHEN p.stock > 0 THEN 0 ELSE 1 END ASC, p.id DESC"; 
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $consulta = $stmt;

} catch (PDOException $e) {
    die("Error en el catálogo: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>LUVEN | Tienda de Repuestos</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="body-full-page">

<header class="main-header">
    <nav class="navbar">
        <div class="logo-area">
            <a href="../index.html">
                <img src="../assets/img/logos/logo de enpresa.png" alt="Logo" class="site-logo">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="nosotros.html">QUIENES SOMOS</a></li>
            <li><a href="servisios.php">SERVICIOS</a></li>
            <li><a href="productos.php">TIENDA</a></li>
            <li><a href="contacto.html">CONTACTO</a></li>
        </ul>
        <div class="nav-actions">
            <a href="cita.php" class="btn-contactenos">AGENDAR CITA</a>
        </div>
    </nav>
</header>

<div class="tienda-wrapper">
    
    <div class="ebay-search-container">
        <form action="productos.php" method="GET" class="ebay-search-form">
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon-inside"></i>
                <input type="text" name="buscar" id="buscar-producto" placeholder="Buscar cualquier cosa en repuestos..." value="<?php echo htmlspecialchars($buscar); ?>">
            </div>
            
            <div class="search-select-wrapper">
                <select name="categoria_id" id="filtrar-categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if(!empty($marcaFiltrada)): ?>
                <input type="hidden" name="marca" value="<?php echo htmlspecialchars($marcaFiltrada); ?>">
            <?php endif; ?>

            <?php if(!empty($modeloFiltrado)): ?>
                <input type="hidden" name="modelo" value="<?php echo htmlspecialchars($modeloFiltrado); ?>">
            <?php endif; ?>
            
            <button type="submit" class="btn-ebay-search">Buscar</button>
        </form>
    </div>

    <div class="hero-tienda">
        <div class="hero-content">
            <h1>Repuestos y Lubricantes Originales</h1>
            <p>Garantiza el rendimiento de tu vehículo con piezas seleccionadas por especialistas.</p>
        </div>
        <div class="hero-icon">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <div class="marcas-vehiculos-section">
        <h2 class="tienda-subtitulo">Busca por marca de vehículo</h2>
        <div class="marcas-grid">
            <?php if (!empty($marcas_dinamicas)): ?>
                <?php foreach ($marcas_dinamicas as $marca): ?>
                    <a href="productos.php?marca=<?php echo urlencode($marca['slug']); ?><?php echo !empty($cat_id) ? '&categoria_id='.$cat_id : ''; ?><?php echo !empty($modeloFiltrado) ? '&modelo='.$modeloFiltrado : ''; ?>" class="marca-item <?php echo ($marcaFiltrada == $marca['slug']) ? 'marca-activa' : ''; ?>">
                        <div class="marca-circulo">
                            <img src="../assets/img/marcas/<?php echo htmlspecialchars($marca['imagen']); ?>" alt="<?php echo htmlspecialchars($marca['nombre']); ?>" class="marca-logo">
                        </div>
                        <span class="marca-nombre"><?php echo htmlspecialchars($marca['nombre']); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #64748b; font-size: 0.95rem; grid-column: 1/-1; text-align: center;">No hay marcas registradas.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="marcas-vehiculos-section" style="margin-top: 30px;">
        <h2 class="tienda-subtitulo">Busca por modelo de auto</h2>
        <div class="marcas-grid">
            <?php if (!empty($modelos_dinamicos)): ?>
                <?php foreach ($modelos_dinamicos as $mod): ?>
                    <a href="productos.php?modelo=<?php echo $mod['id']; ?><?php echo !empty($cat_id) ? '&categoria_id='.$cat_id : ''; ?><?php echo !empty($marcaFiltrada) ? '&marca='.urlencode($marcaFiltrada) : ''; ?>" class="marca-item <?php echo ($modeloFiltrado == $mod['id']) ? 'marca-activa' : ''; ?>">
                        <div class="marca-circulo">
                            <img src="../assets/img/modelos/<?php echo htmlspecialchars($mod['imagen_modelo']); ?>" alt="Modelo" class="marca-logo">
                        </div>
                        <span class="marca-nombre">
                            <?php echo htmlspecialchars($mod['nombre_modelo']); ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #64748b; font-size: 0.95rem; grid-column: 1/-1; text-align: center;">No hay modelos registrados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
        <h2 class="seccion-titulo" style="margin-bottom: 0; font-size: 1.5rem; color: #0f172a; font-weight: 700;">
            <?php 
                if (!empty($buscar) || !empty($cat_id) || !empty($marcaFiltrada) || !empty($modeloFiltrado)) {
                    echo "Resultados de búsqueda";
                } else {
                    echo "Los más vendidos en Repuestos";
                }
            ?>
        </h2>
        <?php if (!empty($buscar) || !empty($cat_id) || !empty($marcaFiltrada) || !empty($modeloFiltrado)): ?>
            <a href="productos.php" style="color: #3b82f6; text-decoration: none; font-weight: 600; font-size: 0.95rem;">
                <i class="fa-solid fa-rotate-left"></i> Ver todo el catálogo
            </a>
        <?php endif; ?>
    </div>

    <div class="tienda-layout-grid">
        
        <aside class="sidebar-categorias">
            <h3>Categorías</h3>
            <ul class="lista-cat-links">
                <li>
                    <a href="productos.php?<?php echo !empty($marcaFiltrada) ? 'marca='.urlencode($marcaFiltrada) : ''; ?><?php echo !empty($modeloFiltrado) ? '&modelo='.urlencode($modeloFiltrado) : ''; ?>" class="<?php echo empty($cat_id) ? 'activa' : ''; ?>">
                        <i class="fa-solid fa-border-all"></i> Todo el catálogo
                    </a>
                </li>
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <a href="productos.php?categoria_id=<?php echo $cat['id']; ?><?php echo !empty($marcaFiltrada) ? '&marca='.urlencode($marcaFiltrada) : ''; ?><?php echo !empty($modeloFiltrado) ? '&modelo='.urlencode($modeloFiltrado) : ''; ?><?php echo !empty($buscar) ? '&buscar='.urlencode($buscar) : ''; ?>" 
                            class="<?php echo ($cat_id == $cat['id']) ? 'activa' : ''; ?>">
                            <i class="fa-solid fa-chevron-right"></i> <?php echo htmlspecialchars($cat['nombre']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="main-productos-container">
            <div class="productos-grid">
                <?php if ($consulta->rowCount() > 0): ?>
                    
                    <?php while ($prod = $consulta->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="producto-card">
                            <div class="producto-imagen-wrapper">
                                <?php if ($prod['stock'] == 0): ?>
                                    <span class="badge-stock" style="background-color: #ef4444;">Agotado</span>
                                <?php elseif ($prod['stock'] <= 3 && $prod['stock'] > 0): ?>
                                    <span class="badge-stock" style="background-color: #f59e0b;">¡Pocas Unidades!</span>
                                <?php endif; ?>

                                <?php if (!empty($prod['imagen'])): ?>
                                    <img src="../assets/img/productos/<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>" class="producto-img">
                                <?php else: ?>
                                    <i class="fa-solid fa-box no-image-icon"></i>
                                <?php endif; ?>
                            </div>

                            <div class="producto-info">
                                <h3 class="producto-nombre"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                                <p class="producto-descripcion"><?php echo htmlspecialchars($prod['descripcion'] ?? 'Sin especificaciones técnicas adicionales.'); ?></p>
                                
                                <div class="producto-meta">
                                    <span class="producto-precio">S/. <?php echo number_format($prod['precio'], 2); ?></span>
                                    <a href="detalle_producto.php?id=<?php echo $prod['id']; ?>" class="btn-detalles">
                                        Ver <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?> 

                <?php else: ?>
                    <div class="tienda-vacia" style="width: 100%; grid-column: 1/-1; text-align: center; padding: 50px 0;">
                        <i class="fa-solid fa-cubes-blur" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                        <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No se encontraron productos disponibles bajo esta selección.</p>
                    </div>
                <?php endif; ?>
            </div>
            <a href="../Frontend/carito.php" class="whatsapp-btn" title="Ir al carrito">
                <i class="fa-solid fa-cart-shopping" style="color: white; font-size: 20px;"></i>
            </a>
        </main>

    </div>
</div>
<script src="../assets/js/tienda.js"></script>
</body>
</html>