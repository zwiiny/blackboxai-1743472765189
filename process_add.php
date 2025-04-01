<?php
session_start();
require_once 'config.php';

// Redirecionar se não autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service_history = trim($_POST['service_history'] ?? '');

    // Validação básica
    if (empty($name) || empty($phone)) {
        $_SESSION['error'] = 'Nome e telefone são campos obrigatórios';
        header('Location: add_client.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO clients (name, phone, email, service_history) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $service_history]);
        
        $_SESSION['success'] = 'Cliente adicionado com sucesso!';
        header('Location: client_list.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erro ao adicionar cliente: ' . $e->getMessage();
        header('Location: add_client.php');
        exit;
    }
} else {
    header('Location: add_client.php');
    exit;
}
?>