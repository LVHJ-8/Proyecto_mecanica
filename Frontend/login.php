<?php
// Control de errores por si necesitas depurar el backend después
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logos/logo de enpresa.png" type="image/png">
    <title>LUVEN | Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="auth-body">

    <div class="login-contenedor">
        <div class="auth-card">
            
            <div class="auth-header">
                <img src="../assets/img/logos/logo de enpresa.png" alt="Logo LUVEN" class="auth-logo">
                <h2>Iniciar Sesión</h2>
                <p>Ingresa tus credenciales para acceder a tu cuenta y agendar tus citas.</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                <div class="alerta-autenticacion">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Para continuar es necesario que se registre o inicie sesión.</span>
                </div>
            <?php endif; ?>

            <div id="alerta-error-global" class="alerta-error-box" style="display: none;"></div>

            <form id="formulario-login" action="../adisionales/auth.php?accion=login" method="POST" novalidate>
                
                <?php 
                $redirigir = isset($_GET['redirigir']) ? $_GET['redirigir'] : ''; 
                ?>
                <input type="hidden" name="redirigir" value="<?php echo htmlspecialchars($redirigir); ?>">

                <div class="grupo-input">
                    <label for="log-correo">Correo Electrónico</label>
                    <input type="email" id="log-correo" name="correo" placeholder="ejemplo@correo.com">
                    <span class="error-individual" id="error-correo"></span>
                </div>

                <div class="grupo-input">
                    <label for="log-password">Contraseña</label>
                    <input type="password" id="log-password" name="password" placeholder="Ingresa tu contraseña">
                    <span class="error-individual" id="error-password"></span>
                </div>

                <button type="submit" id="btn-ingresar" class="btn-auth-enviar">Ingresar al Sistema</button>
            </form>

            <div class="auth-footer">
                <p>¿Aún no tienes una cuenta? <a href="registro.php?redirigir=<?php echo urlencode($redirigir); ?>">Regístrate aquí</a></p>
                <br>
                <a href="../index.html" class="btn-volver-inicio">← Volver al Inicio</a>
            </div>

        </div>
    </div>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>