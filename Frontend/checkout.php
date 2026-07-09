<?php
session_start();
require_once '../adisionales/conexion.php';

if (empty($_SESSION['carrito'])) {
    header("Location: carito.php");
    exit;
}

$total = 0;
foreach ($_SESSION['carrito'] as $id => $item) {
    $total += ($item['precio'] * $item['cantidad']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pedido | LUVEN</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<div class="tienda-wrapper" style="padding: 40px;">
    <h2>Finalizar Pedido</h2>
    <form action="procesar_pedido.php" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            <div>
                <h3>Datos de envío</h3>
                <input type="text" name="nombre" placeholder="Nombre completo" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
                <input type="text" name="direccion" placeholder="Dirección de entrega" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
                <input type="text" name="telefono" placeholder="Teléfono" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
            </div>
            <div>
                <h3>Resumen de compra</h3>
                <p><strong>Total a pagar: S/. <?php echo number_format($total, 2); ?></strong></p>
                <button type="submit" class="btn-temu-orden" style="width: 100%; padding: 15px;">Confirmar Pedido</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>