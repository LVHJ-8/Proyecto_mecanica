<?php
// 1. CONTROL DE ERRORES OPERATIVOS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../adisionales/conexion.php"; 

try {
    $stmt = $pdo->query("SELECT id, nombre, descripcion, duracion_minutos, precio FROM servicios ORDER BY id DESC");
    $servicios = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("<div style='padding:20px; background:#fde8e8; color:#9b1c1c; font-family:sans-serif;'>
            <h3>❌ Error al conectar con el catálogo:</h3>" . $e->getMessage() . "
        </div>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>LUVEN Taller & Repuestos | Servicios</title>
    <!-- Ajustamos la ruta para que busque los estilos saliendo de Frontend -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="top-bar">
    <div class="top-bar-container">
        <span class="contact-info">LUVEN Taller & Repuestos</span>
        <div class="top-bar-links">
            <?php 
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (isset($_SESSION['usuario_id'])): ?>
                <a href="carito.php" class="top-link"><i class="fa-solid fa-cart-shopping"></i> Ver Carrito</a>
                <span class="separator">|</span>
                <a href="logout.php" class="top-link">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php?redirigir=carrito&error=1" class="top-link"><i class="fa-solid fa-cart-shopping"></i> Ver Carrito</a>
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
                <a href="../index.html">
                    <img src="../assets/img/logos/logo de enpresa.png" alt="Logo Taller Mecánico" class="site-logo">
                </a>
            </div>

            <ul class="nav-links">
                <li><a href="nosotros.html">QUIENES SOMOS</a></li>
                <li><a href="servicios.php" class="active">SERVICIOS</a></li>
                <li><a href="productos.php">TIENDA</a></li>
                <li><a href="contacto.html">CONTACTO</a></li>
            </ul>

            <div class="nav-actions">
                <a href="cita.php" class="btn-agendar-nav">AGENDAR CITA</a>
            </div>
        </nav>
    </header>

    <main class="hero-container">
        <button class="slider-arrow prev-arrow" onclick="cambiarSlide(-1)" aria-label="Anterior">&#10094;</button>

        <div class="hero-content">
            <div class="hero-text-side">
                <h1 class="hero-title">
                    CONOCE <br>
                    <span class="highlight-red">NUESTROS</span> <br>
                    SERVICIOS
                </h1>
                <div class="hero-badge">
                    <span class="badge-text">Garantía y Calidad Integral</span>
                </div>
            </div>

            <div class="hero-image-side">
                <div class="diagonal-bg"></div>
                
                <div class="slide active">
                    <img src="../assets/img/logos/mecanico.jfif" alt="Servicios 1" class="floating-image">
                </div>
                <div class="slide">
                    <img src="../assets/img/logos/enbrage.jfif" alt="Servicios 2" class="floating-image">
                </div>
                <div class="slide">
                    <img src="../assets/img/logos/llaves.jfif" alt="Servicios 3" class="floating-image">
                </div>
                <div class="slide">
                    <img src="../assets/img/logos/motor.jfif" alt="Servicios 4" class="floating-image">
                </div>
                <div class="slide">
                    <img src="../assets/img/logos/repuestos.jfif" alt="Servicios 5" class="floating-image">
                </div>
            </div>
        </div>

        <button class="slider-arrow next-arrow" onclick="cambiarSlide(1)" aria-label="Siguiente">&#10095;</button>
        
        <a href="https://wa.me/51906969275" class="whatsapp-btn" target="_blank" rel="noopener noreferrer">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
        </a>
    </main>
    <section class="servicios-publico-section">
        <div class="servicios-grid">
            
            <?php if (empty($servicios)): ?>
                <div class="no-servicios">
                    <p>🔧 Estamos actualizando nuestro catálogo de operaciones mecánicas en línea.</p>
                </div>
            <?php else: ?> 
                
                <?php foreach ($servicios as $servicio): ?>
                    <div class="servicio-card">
                        <div class="servicio-icon">🔧</div>
                        <h3><?php echo htmlspecialchars($servicio['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($servicio['descripcion'] ?? 'Mantenimiento preventivo general.'); ?></p>
                        
                        
                        <div class="servicio-footer">
                            <span class="servicio-precio">S/ <?php echo number_format($servicio['precio'], 2); ?></span>
                            <a href="../modulos/citas/agendar.php?servicio_id=<?php echo $servicio['id']; ?>" class="btn-reservar-servicio">
                                Reservar Cita
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>
    
    <footer class="main-footer">
        <p>&copy; 2026 LUVEN Taller & Repuestos. Todos los derechos reservados.</p>
    </footer>
    <script src="../assets/js/servisios.js"></script>

</body>
</html>