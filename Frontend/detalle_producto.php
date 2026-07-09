<?php
session_start();
require_once __DIR__ . '/../adisionales/conexion.php';

// Obtener el ID del producto
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta del producto principal
$stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado.");
}

// Consulta de productos relacionados
$stmt_rel = $pdo->prepare("SELECT * FROM productos WHERE categoria_id = ? AND id != ? AND activo = 1 LIMIT 4");
$stmt_rel->execute([$producto['categoria_id'], $id_producto]);
$relacionados = $stmt_rel->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LUVEN | <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header class="main-header">
    <nav class="navbar">
        <div class="logo-area">
            <a href="../index.php"><img src="../assets/img/logos/logo de enpresa.png" alt="Logo" class="site-logo"></a>
        </div>
        <ul class="nav-links">
            <li><a href="productos.php">TIENDA</a></li>
        </ul>
    </nav>
</header>

<div class="tienda-wrapper" style="padding: 40px 20px;">
    <div class="detalle-container" style="display: flex; gap: 40px; flex-wrap: wrap;">
        <div class="detalle-imagen" style="flex: 1; min-width: 300px;">
            <img src="../assets/img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="width: 100%; border-radius: 8px;">
        </div>
        
        <div class="detalle-info" style="flex: 1; min-width: 300px;">
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            <p style="font-size: 1.5rem; color: #e11d48; font-weight: bold;">S/. <?php echo number_format($producto['precio'], 2); ?></p>
            <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
            
            <div class="opciones-compra" style="margin-top: 20px; display: flex; gap: 15px; align-items: center;">
                <label for="cantidad_producto">Cantidad:</label>
                <input type="number" id="cantidad_producto" value="1" min="1" style="width: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                
                <button type="button" onclick="agregarAlCarrito(<?php echo $producto['id']; ?>)" class="btn-agregar" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fa-solid fa-cart-plus"></i> Añadir al carrito
                </button>
            </div>
        </div>
    </div>

    <div class="seccion-relacionados" style="margin-top: 60px; border-top: 2px solid #f1f5f9; padding-top: 30px;">
        <h3 style="font-size: 1.5rem; color: #0f172a; margin-bottom: 25px;">También te podría interesar</h3>
        <div class="productos-grid">
            <?php if (count($relacionados) > 0): ?>
                <?php foreach ($relacionados as $rel): ?>
                    <div class="producto-card">
                        <div class="producto-imagen-wrapper">
                            <img src="../assets/img/productos/<?php echo htmlspecialchars($rel['imagen']); ?>" alt="<?php echo htmlspecialchars($rel['nombre']); ?>" class="producto-img">
                        </div>
                        <div class="producto-info">
                            <h4 class="producto-nombre"><?php echo htmlspecialchars($rel['nombre']); ?></h4>
                            <div class="producto-meta">
                                <span class="producto-precio">S/. <?php echo number_format($rel['precio'], 2); ?></span>
                                <a href="detalle_producto.php?id=<?php echo $rel['id']; ?>" class="btn-detalles">Ver <i class="fa-solid fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay más productos en esta categoría.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<a href="../Frontend/carito.php" style="position: fixed; bottom: 20px; right: 20px; background: #3b82f6; padding: 15px 20px; border-radius: 50%; color: white; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2); z-index: 1000;">
    <i class="fa-solid fa-cart-shopping" style="font-size: 20px;"></i>
</a>

<script>
function agregarAlCarrito(idProducto) {
    // Capturamos el valor de la cantidad
    let cantidad = document.getElementById('cantidad_producto').value;
    
    let formData = new FormData();
    formData.append('id_producto', idProducto);
    formData.append('cantidad', cantidad); // Enviamos la cantidad

    fetch('agregar_al_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('¡Producto añadido correctamente!');
        } else {
            alert('Error: ' + (data.mensaje || 'No se pudo agregar'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al conectar con el servidor.');
    });
}
</script>
</body>
</html>