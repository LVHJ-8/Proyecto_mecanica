<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Opcional: Podrías añadir código para borrar la imagen del servidor antes de borrar el registro
    $stmt = $pdo->prepare("DELETE FROM marcas WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: CRUDMarcas.php");
exit;