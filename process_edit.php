<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: client_list.php');
    exit;
}

$clientId = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service_history = trim($_POST['service_history'] ?? '');

    // Basic validation
    if (empty($name) || empty($phone)) {
        $_SESSION['error'] = 'Name and phone are required fields';
        header("Location: edit_client.php?id=$clientId");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, service_history = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $service_history, $clientId]);
        
        $_SESSION['success'] = 'Client updated successfully!';
        header("Location: edit_client.php?id=$clientId");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error updating client: ' . $e->getMessage();
        header("Location: edit_client.php?id=$clientId");
        exit;
    }
} else {
    header('Location: client_list.php');
    exit;
}
?>