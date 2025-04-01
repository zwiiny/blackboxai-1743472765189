<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get user information
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get total client count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
$clientCount = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Shop - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 flex-shrink-0">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">Barber Shop</h1>
                <p class="text-gray-400 text-sm">Welcome, <?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <nav class="p-4">
                <ul>
                    <li class="mb-2">
                        <a href="dashboard.php" class="flex items-center p-2 text-white bg-gray-700 rounded">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="client_list.php" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-users mr-2"></i> Clients
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-calendar-alt mr-2"></i> Appointments
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-chart-bar mr-2"></i> Reports
                        </a>
                    </li>
                    <li class="mt-8">
                        <a href="logout.php" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-700 rounded">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Dashboard</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo date('l, F j, Y'); ?></span>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Clients Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Total Clients</h3>
                                <p class="text-3xl font-bold mt-2"><?php echo $clientCount; ?></p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="client_list.php" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            View all clients
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <!-- Appointments Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Today's Appointments</h3>
                                <p class="text-3xl font-bold mt-2">0</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-calendar-check text-green-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            View calendar
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <!-- Revenue Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 uppercase tracking-wider text-sm font-semibold">Monthly Revenue</h3>
                                <p class="text-3xl font-bold mt-2">$0.00</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-dollar-sign text-purple-500 text-xl"></i>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex items-center text-blue-500 hover:text-blue-700">
                            View reports
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Recent Clients -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-lg">Recent Clients</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
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