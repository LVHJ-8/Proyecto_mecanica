<?php
// 1. Iniciar la sesión para poder acceder a ella
session_start();

// 2. Limpiar el array de variables de sesión
$_SESSION = array();

// 3. Destruir la cookie de sesión (opcional, pero recomendado por seguridad)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión en el servidor
session_destroy();

// 5. Redirigir al usuario al index (o login)
header("Location: ../index.html");
exit;
?>