<?php
// 1. INICIAR EL SISTEMA DE SESIONES (Obligatorio para $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. CONEXIÓN A LA BASE DE DATOS (Al estar en la misma carpeta, se llama directo)
require_once "conexion.php";

// Capturar la acción enviada desde el formulario por la URL (?accion=...)
$accion = $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =================================================================
    // PROCESO A: REGISTRO DE USUARIOS (Solución al error de columna)
    // =================================================================
    if ($accion === 'registrar') {
        $nombre   = trim($_POST['nombre']);
        $correo   = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        $password = $_POST['password'];

        // ENCRIPTACIÓN SEGURA
        $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

        try {
            // CORRECCIÓN: Se cambió 'contrasena' por 'password' y agregamos el 'rol' automático como 'cliente'
            $sql = "INSERT INTO usuarios (nombre, correo, telefono, password, rol) VALUES (?, ?, ?, ?, 'cliente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $correo, $telefono, $password_encriptada]);

            // Redirige al login que está dentro de la carpeta Frontend
            header("Location: ../Frontend/login.php?registro=exito");
            exit;

        } catch (PDOException $e) {
            // Esto atrapará los errores de forma limpia en pantalla
            die("Error crítico al registrar: " . $e->getMessage());
        }
    }

    // =================================================================
    // PROCESO B: INICIO DE SESIÓN / LOGIN (Con redirección inteligente)
    // =================================================================
    if ($accion === 'login') {
        $correo   = trim($_POST['correo']);
        $password = $_POST['password'];

        try {
            $sql = "SELECT * FROM usuarios WHERE correo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // CORRECCIÓN: Usamos $usuario['password'] para verificar la clave encriptada
            if ($usuario && password_verify($password, $usuario['password'])) {
                
                // GUARDAR DATOS EN LA SESIÓN GLOBAL DEL NAVEGADOR
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['rol']        = $usuario['rol']; // Almacena si es 'admin' o 'cliente'

                // REDIRECCIÓN INTELIGENTE SEGÚN EL ROL
                if ($_SESSION['rol'] === 'admin') {
                    // Si eres administrador, te envía a la vista del Panel
                    header("Location: ../ADMIN/admin.php");
                } else {
                    // Si es un cliente normal, va a la raíz pública
                    header("Location: ../index.html");
                }
                exit;
            } else {
                // Si falla, regresa al formulario con el aviso de error
                header("Location: ../Frontend/login.php?error=credenciales");
                exit;
            }

        } catch (PDOException $e) {
            die("Error crítico en el login: " . $e->getMessage());
        }
    }
}