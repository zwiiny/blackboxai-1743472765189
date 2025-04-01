<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service_history = trim($_POST['service_history'] ?? '');

    // Basic validation
    if (empty($name) || empty($phone)) {
        $_SESSION['error'] = 'Name and phone are required fields';
        header('Location: add_client.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO clients (name, phone, email, service_history) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $service_history]);
        
        $_SESSION['success'] = 'Client added successfully!';
        header('Location: client_list.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error adding client: ' . $e->getMessage();
        header('Location: add_client.php');
        exit;
    }
} else {
    header('Location: add_client.php');
    exit;
}
?>