<?php
session_start();
require_once '../adisionales/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['carrito'])) {
    try {
        $pdo->beginTransaction();

        // 1. Insertar el pedido principal
        $stmt = $pdo->prepare("INSERT INTO pedidos (nombre, direccion, telefono, total, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$_POST['nombre'], $_POST['direccion'], $_POST['telefono'], $total]);
        $pedido_id = $pdo->lastInsertId();

        // 2. Insertar cada producto del carrito
        $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
        
        foreach ($_SESSION['carrito'] as $id => $item) {
            $stmt_detalle->execute([$pedido_id, $id, $item['cantidad'], $item['precio']]);
        }

        $pdo->commit();
        
        // 3. Limpiar carrito
        $_SESSION['carrito'] = [];
        
        echo "<h1>Pedido procesado con éxito</h1><a href='productos.php'>Volver a la tienda</a>";
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al procesar el pedido: " . $e->getMessage());
    }
} else {
    header("Location: carito.php");
}
?>