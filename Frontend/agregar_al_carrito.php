<?php
session_start();
require_once '../adisionales/conexion.php'; 

// Verificamos si la petición es correcta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id = intval($_POST['id_producto']);
    
    // Capturamos la cantidad (si no llega nada, por defecto será 1)
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
    if ($cantidad < 1) $cantidad = 1; // Aseguramos que nunca sea menor a 1

    // Consultamos el producto
    $stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Inicializamos el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Si el producto ya existe en el carrito, sumamos la cantidad
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
        } else {
            // Si es nuevo, lo agregamos con la cantidad solicitada
            $_SESSION['carrito'][$id] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad
            ];
        }

        // Respondemos con éxito
        echo json_encode(['status' => 'success', 'mensaje' => 'Producto añadido al carrito']);
        exit;
    }
}

// Si algo falló
echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo añadir al carrito']);
?>