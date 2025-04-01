<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Check if client ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: client_list.php');
    exit;
}

$clientId = (int)$_GET['id'];
$errors = [];
$success = false;

// Get current client data
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$clientId]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: client_list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service_history = trim($_POST['service_history'] ?? '');

    // Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone is required';
    } elseif (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
        $errors['phone'] = 'Invalid phone number format';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE clients SET name = ?, phone = ?, email = ?, service_history = ? WHERE id = ?");
            if ($stmt->execute([$name, $phone, $email, $service_history, $clientId])) {
                $success = true;
                // Refresh client data
                $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
                $stmt->execute([$clientId]);
                $client = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Error updating client: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Shop - Edit Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'dashboard.php'; ?>

    <main class="p-6 ml-64">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Client</h2>
            <a href="client_list.php" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Back to Clients
            </a>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                Client updated successfully!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 mb-2" for="name">Full Name*</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($client['name'] ?? ''); ?>" required
                            class="w-full px-3 py-2 border <?php echo isset($errors['name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="phone">Phone Number*</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>" required
                            class="w-full px-3 py-2 border <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['phone'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['phone']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email'] ?? ''); ?>"
                            class="w-full px-3 py-2 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['email'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['email']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="service_history">Service History</label>
                        <textarea id="service_history" name="service_history" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($client['service_history'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="reset" class="mr-3 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md">
                        Reset
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md">
                        <i class="fas fa-save mr-1"></i> Update Client
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>
</html>