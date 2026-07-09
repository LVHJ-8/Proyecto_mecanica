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
    <title>LUVEN | Crear Cuenta</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="auth-body">

    <div class="registro-contenedor">
        <div class="auth-card">
            
            <div class="auth-header">
                <img src="../assets/img/logos/logo de enpresa.png" alt="Logo LUVEN" class="auth-logo">
                <h2>Registrarse en LUVEN</h2>
                <p>Crea tu cuenta para gestionar tus servicios mecánicos y agendar citas.</p>
            </div>
            <div id="alerta-error-global" class="alerta-error-box" style="display: none;"></div>

            <form id="formulario-registro" action="../adisionales/auth.php?accion=registrar" method="POST" novalidate>

                <div class="grupo-input">
                    <label for="reg-nombre">Nombre Completo</label>
                    <input type="text" id="reg-nombre" name="nombre" placeholder="Ej. Juan Pérez">
                    <span class="error-individual" id="error-nombre"></span>
                </div>

                <div class="grupo-input">
                    <label for="reg-correo">Correo Electrónico</label>
                    <input type="email" id="reg-correo" name="correo" placeholder="ejemplo@correo.com">
                    <span class="error-individual" id="error-correo"></span>
                </div>

                <div class="grupo-input">
                    <label for="reg-telefono">Teléfono / Celular</label>
                    <input type="tel" id="reg-telefono" name="telefono" placeholder="Ej. 906969275" maxlength="9">
                    <span class="error-individual" id="error-telefono"></span>
                </div>

                <div class="grupo-input">
                    <label for="reg-password">Contraseña</label>
                    <input type="password" id="reg-password" name="password" placeholder="Mínimo 8 caracteres">
                    <span class="error-individual" id="error-password"></span>
                </div>

                <div class="grupo-input">
                    <label for="reg-confirmar">Confirmar Contraseña</label>
                    <input type="password" id="reg-confirmar" name="confirmar_password" placeholder="Repite tu contraseña">
                    <span class="error-individual" id="error-confirmar"></span>
                </div>

                <button type="submit" id="btn-registrar" class="btn-auth-enviar">Crear Mi Cuenta</button>
            </form>

            <div class="auth-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión aquí</a></p>
                <br>
                <a href="../index.html" class="btn-volver-inicio">← Volver al Inicio</a>
            </div>

        </div>
    </div>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>