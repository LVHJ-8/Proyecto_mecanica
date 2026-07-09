<?php
// 1. Iniciar la sesión para guardar los datos del usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Importar la conexión a la base de datos (ajusta la ruta si tu carpeta controladores está en otro nivel)
require_once "conexion.php";

// 3. Validar que los datos vengan estrictamente por el método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Limpiar espacios en blanco de las entradas
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validar que los campos no se envíen vacíos
    if (empty($correo) || empty($password)) {
        header("Location: ../Frontend/login.php?error=vacio");
        exit;
    }

    try {
        // 4. Buscar al usuario por su correo electrónico en la tabla usuarios
        // Nota: Asegúrate de que tu tabla tenga las columnas: id, nombre, correo, password, rol
        $stmt = $pdo->prepare("SELECT id, nombre, correo, password, rol FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 5. Verificar si el usuario existe y si la contraseña coincide
        if ($usuario && password_verify($password, $usuario['password'])) {
            
            // Regenerar el ID de sesión por seguridad frente a ataques de fijación
            session_regenerate_id(true);

            // 6. Almacenar datos clave en la sesión global ($_SESSION)
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['correo']     = $usuario['correo'];
            $_SESSION['rol']        = $usuario['rol']; // 'admin' o 'cliente'

            // 7. REDIRECCIÓN INTELIGENTE SEGÚN EL ROL
            if ($_SESSION['rol'] === 'admin') {
                // Si el rol es administrador, ingresa directamente al panel de control
                header("Location: ../ADMIN/admin.php");
            } else {
                // Si es un cliente común, lo mandamos al index o catálogo principal
                header("Location: ../index.html");
            }
            exit;

        } else {
            // Error: Contraseña incorrecta o el correo no está registrado
            header("Location: ../Frontend/login.php?error=incorrecto");
            exit;
        }

    } catch (PDOException $e) {
        // En caso de un fallo crítico en la consulta SQL
        die("Error en el sistema de autenticación: " . $e->getMessage());
    }

} else {
    // Si intentan entrar al archivo escribiendo la URL directa por GET, los saca al formulario
    header("Location: ../Frontend/login.php");
    exit;
}

