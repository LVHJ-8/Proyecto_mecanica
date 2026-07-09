<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '../../Proyecto_mecanica/adisionales/conexion.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: CRUDCategorias.php");
exit;