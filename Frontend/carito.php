<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicialización de variables
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$carrito_vacio = empty($carrito);
$total_general = 0; // Se inicializa en 0 para evitar errores
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>LUVEN | Mi Carrito de Compras</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="top-bar">
        <div class="top-bar-container">
            <span class="contact-info">LUVEN Taller & Repuestos</span>
            <div class="top-bar-links">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="carito.php" class="top-link"><i class="fa-solid fa-cart-shopping"></i> Ver Carrito</a>
                    <span class="separator">|</span>
                    <a href="logout.php" class="top-link">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="carito.php" class="top-link"><i class="fa-solid fa-cart-shopping"></i> Ver Carrito</a>
                    <span class="separator">|</span>
                    <a href="login.php" class="top-link">Iniciar Sesión</a>
                    <span class="separator">|</span>
                    <a href="registro.php" class="top-link">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header class="main-header">
        <nav class="navbar">
            <div class="logo-area">
                <a href="../index.php">
                    <img src="../assets/img/logos/logo de enpresa.png" alt="Logo LUVEN" class="site-logo">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="nosotros.html">QUIENES SOMOS</a></li>
                <li><a href="servicios.php">SERVICIOS</a></li>
                <li><a href="productos.php" class="active">TIENDA</a></li>
                <li><a href="contacto.html">CONTACTO</a></li>
            </ul>
            <div class="nav-actions">
                <a href="cita.php" class="btn-agendar-nav">AGENDAR CITA</a>
            </div>
        </nav>
    </header>

    <div class="carrito-main-container">

        <div class="carrito-banner-envio">
            <i class="fa-solid fa-truck-fast"></i>
            <span><strong>Envío </strong> (En todas tus compras de repuestos y piezas seleccionadas)</span>
        </div>

        <div class="carrito-layout">
            
            <div class="carrito-columna-productos">
                
                <?php if ($carrito_vacio): ?>
                    <div class="carrito-estado-vacio">
                        <div class="icon-wrap">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <h3>El carrito de compras está vacío</h3>
                        <p>Agrega tus repuestos o artículos favoritos.</p>
                        
                        <div class="carrito-vacio-botones">
                            <a href="login.php?redirigir=carrito&error=1" class="btn-temu-naranja">Iniciar sesión/Registrarse</a>
                            <a href="productos.php" class="btn-temu-blanco">Comienza a comprar</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($carrito as $id => $item): 
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $total_general += $subtotal;
                    ?>
                        <div class="carrito-tarjeta-item" style="padding: 15px; border-bottom: 1px solid #ddd; margin-bottom: 10px;">
                            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                            <p>Precio: S/ <?php echo number_format($item['precio'], 2); ?> | Cantidad: <?php echo $item['cantidad']; ?></p>
                            <p><strong>Subtotal: S/ <?php echo number_format($subtotal, 2); ?></strong></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="carrito-columna-resumen">
                <div class="carrito-resumen-card">
                    <h3>Resumen del pedido</h3>
                    
                    <div class="resumen-fila-total">
                        <span>Total</span>
                        <strong>S/ <?php echo number_format($total_general, 2); ?></strong>
                    </div>
                    
                    <p class="resumen-nota">Consulta el monto final de tu pago real.</p>
                    
                    <button class="btn-temu-orden" 
                            <?php echo $carrito_vacio ? 'disabled' : ''; ?>
                            onclick="window.location.href='checkout.php'"> Hacer pedido (<?php echo count($carrito); ?>)
                    </button>

                    <div class="carrito-confianza-lista">
                        <div class="confianza-item">
                            <i class="fa-solid fa-lock text-verde"></i>
                            <span>No se te cobrará hasta que revises este pedido en la página siguiente.</span>
                        </div>
                        <div class="confianza-item">
                            <i class="fa-solid fa-shield-halved text-verde"></i>
                            <span><strong>Opciones de pago seguro:</strong> LUVEN se compromete a proteger tu información de pago.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>