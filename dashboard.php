<?php
session_start();
require_once 'config.php';

// Redirecionar se não autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Obter informações do usuário
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Obter contagem total de clientes
$stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
$clientCount = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia - Painel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Barra lateral -->
        <div class="bg-gray-800 text-white w-64 flex-shrink-0">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">Barbearia</h1>
                <p class="text-gray-400 text-sm">Bem-vindo, <?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <nav class="p-4">
                <ul>
                    <li class="mb-2">
                        <a href="dashboard.php" class="flex items-center p-2 text-white bg-gray-700 rounded">
                            <i class="fas fa-tachometer-alt mr-2"></i> Painel
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="client_list.php" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-users mr-2"></i> Clientes
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-calendar-alt mr-2"></i> Agendamentos
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-chart-bar mr-2"></i> Relatórios
                        </a>
                    </li>
                    <li class="mt-8">
                        <a href="logout.php" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sair
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Conteúdo principal -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Painel</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo date('l, j \d\e F \d\e Y'); ?></span>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card de Clientes -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Total de Clientes</h3>
                                <p class="text-3xl font-bold mt-2"><?php echo $clientCount; ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="client_list.php" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            Ver todos os clientes
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <!-- Card de Agendamentos -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Agendamentos Hoje</h3>
                                <p class="text-3xl font-bold mt-2">0</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-calendar-check text-green-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            Ver calendário
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <!-- Card de Receita -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Receita Mensal</h3>
                                <p class="text-3xl font-bold mt-2">R$ 0,00</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-dollar-sign text-purple-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            Ver relatórios
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Clientes Recentes -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-lg">Clientes Recentes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Visita</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $pdo->query("SELECT name, phone, email, created_at FROM clients ORDER BY created_at DESC LIMIT 5");
                                while ($row = $stmt->fetch()):
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>