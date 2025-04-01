<?php
session_start();
require_once 'config.php';

// Redirecionar se não autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: client_list.php');
    exit;
}

$clientId = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$clientId]);
    
    $_SESSION['success'] = 'Cliente excluído com sucesso!';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erro ao excluir cliente: ' . $e->getMessage();
}

header('Location: client_list.php');
exit;
?>