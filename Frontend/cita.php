<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../adisionales/conexion.php";

// =========================================================================
// PROCESAR EL FORMULARIO DE REGISTRO DE CLIENTE Y CITA (Mismo archivo)
// =========================================================================
$mensaje_exito = "";
$mensaje_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar_todo') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password']; 
    $placa = $_POST['placa'];
    $vehiculo = $_POST['vehiculo'];
    $fecha_registro = $_POST['fecha_registro'];
    $hora_seleccionada = $_POST['hora_seleccionada'];
    
    // 🔴 CAMBIO AQUÍ: Ahora recibimos el ID del servicio seleccionado desde el formulario
    $servicio_id = isset($_POST['servicio_id']) ? $_POST['servicio_id'] : null; 

    try {
        $pdo->beginTransaction(); 

        // 1. Verificar si el correo ya existe
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtCheck->execute([$correo]);
        $usuario = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $usuario_id = $usuario['id'];
        } else {
            $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
            $stmtUser = $pdo->prepare("INSERT INTO usuarios (nombre, correo, telefono, password, rol) VALUES (?, ?, ?, ?, 'cliente')");
            $stmtUser->execute([$nombre, $correo, $telefono, $password_encriptada]);
            $usuario_id = $pdo->lastInsertId();
        }
        // Empaquetamos placa y vehículo dentro de la columna existente 'notas'
        $notas_vehiculo = "Vehículo: " . $vehiculo . " | Placa: " . $placa;

        // 🟢 SOLUCIÓN: Relacionamos la hora de los botones con el ID que pide tu base de datos
        $tabla_horarios = [
            "08:00" => 1,
            "09:30" => 2,
            "11:00" => 3,
            "14:00" => 4,
            "15:30" => 5,
            "17:00" => 6
        ];

        // Si la hora existe en la lista, asigna su ID; de lo contrario, pone 1 por defecto
        $horario_id = isset($tabla_horarios[$hora_seleccionada]) ? $tabla_horarios[$hora_seleccionada] : 1;
        // Preparamos la consulta e insertamos el $servicio_id real
        $stmtCita = $pdo->prepare("INSERT INTO citas (usuario_id, servicio_id, horario_id, fecha, hora, estado, notas) VALUES (?, ?, ?, ?, ?, 'Pendiente', ?)");
        $stmtCita->execute([$usuario_id, $servicio_id, $horario_id, $fecha_registro, $hora_seleccionada, $notas_vehiculo]);

        $pdo->commit(); 
        
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['rol'] = 'cliente';

        $mensaje_exito = "¡Cita reservada con éxito! Tu cuenta ha sido creada.";
    } catch (PDOException $e) {
        $pdo->rollBack(); 
        $mensaje_error = "Error al procesar la reserva: " . $e->getMessage();
    }
}

// =========================================================================
// CONSULTAS PARA CARGAR COMPONENTES DE LA PÁGINA
// =========================================================================
try {
    $stmtServicios = $pdo->query("SELECT id, nombre, precio FROM servicios ORDER BY nombre ASC");
    $servicios = $stmtServicios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar los servicios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>LUVEN | Agendar Cita</title>
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

<div class="cita-container">
    
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alerta-autenticacion" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
            <i class="fa-solid fa-circle-check"></i> <span><?php echo $mensaje_exito; ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alerta-autenticacion" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <i class="fa-solid fa-circle-xmark"></i> <span><?php echo $mensaje_error; ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
        <div class="alerta-autenticacion">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Para continuar es necesario que se registre o inicie sesión.</span>
        </div>
    <?php endif; ?>

    <div class="cita-header">
        <p class="cita-etiqueta">Ahorra tiempo y evita colas</p>
        <h1 class="cita-titulo">Agenda tu Cita</h1>
    </div>

    <form id="form-agendar-cita" class="cita-formulario" action="" method="POST">
        <input type="hidden" name="action" value="registrar_todo">
        <input type="hidden" name="redirigir" value="cita">

        <div class="cita-columna-datos">
            <div class="cita-seccion">
                <h3 class="cita-subtitulo">Datos Personales</h3>
                <div class="cita-campo">
                    <label for="nombre"><i class="fa-solid fa-user"></i> Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
                </div>
                <div class="cita-campo">
                    <label for="correo"><i class="fa-solid fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="cita-campo">
                    <label for="telefono"><i class="fa-solid fa-phone"></i> Teléfono / WhatsApp</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="987654321" required>
                </div>
                <div class="cita-campo">
                    <label for="password"><i class="fa-solid fa-lock"></i> Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Crea tu contraseña segura" required>
                </div>
            </div>

            <div class="cita-seccion">
                <h3 class="cita-subtitulo">Datos del Vehículo</h3>
                <div class="cita-campo">
                    <label for="placa"><i class="fa-solid fa-car"></i> Número de Placa</label>
                    <input type="text" id="placa" name="placa" placeholder="ABC-123" required>
                </div>
                <div class="cita-campo">
                    <label for="vehiculo"><i class="fa-solid fa-wrench"></i> Vehículo (Marca y Modelo)</label>
                    <input type="text" id="vehiculo" name="vehiculo" placeholder="Ej. Toyota Corolla 2022" required>
                </div>
            </div>
        </div>

        <div class="cita-columna-horario">
            <div class="cita-agenda">
                
                <div class="cita-campo" style="margin-bottom: 25px;">
                    <label class="cita-label-principal" for="servicio_id">
                        <i class="fa-solid fa-bell-concierge"></i> 1. Selecciona un Servicio
                    </label>
                    <div style="margin-top: 10px;">
                        <select id="servicio_id" name="servicio_id" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px; background-color: #fff;">
                            <option value="" disabled selected>-- Elige el servicio para tu vehículo --</option>
                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?php echo $servicio['id']; ?>">
                                    <?php echo htmlspecialchars($servicio['nombre']) . " - S/ " . number_format($servicio['precio'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <label class="cita-label-principal">
                    <i class="fa-solid fa-calendar-days"></i> 2. Selecciona una Fecha
                </label>
                <div class="cita-fecha">
                    <input type="date" id="fecha-cita" name="fecha_registro" required>
                </div>

                <div id="container-horas-diarias" class="cita-horas-container" style="margin-top: 25px;">
                    <label class="cita-label-principal">
                        <i class="fa-solid fa-clock"></i> 3. Selecciona un Horario de Atención
                    </label>
                    
                    <div id="lista-horas" class="horarios-grid-mecanica">
                        <button type="button" class="bloque-hora" data-hora="08:00">08:00 AM</button>
                        <button type="button" class="bloque-hora" data-hora="09:30">09:30 AM</button>
                        <button type="button" class="bloque-hora" data-hora="11:00">11:00 AM</button>
                        <button type="button" class="bloque-hora" data-hora="14:00">02:00 PM</button>
                        <button type="button" class="bloque-hora" data-hora="15:30">03:30 PM</button>
                        <button type="button" class="bloque-hora" data-hora="17:00">05:00 PM</button>
                    </div>
                </div>

                <input type="hidden" id="hora-seleccionada" name="hora_seleccionada" required>
                <button type="submit" class="cita-btn-reservar" style="margin-top: 25px;">Registrar y Reservar Cita</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const botonesHora = document.querySelectorAll(".bloque-hora");
        const inputHoraHidden = document.getElementById("hora-seleccionada");

        botonesHora.forEach(boton => {
            boton.addEventListener("click", function() {
                botonesHora.forEach(b => b.classList.remove("active", "seleccionado"));
                this.classList.add("seleccionado");
                inputHoraHidden.value = this.getAttribute("data-hora");
            });
        });
    });
</script>
<script src="../assets/js/agendar.js"></script>
</body>
</html>