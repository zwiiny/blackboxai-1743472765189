<?php
session_start();
require_once 'config.php';

// Redirecionar se não autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service_history = trim($_POST['service_history'] ?? '');

    // Validação
    if (empty($name)) {
        $errors['name'] = 'Nome é obrigatório';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Telefone é obrigatório';
    } elseif (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
        $errors['phone'] = 'Formato de telefone inválido';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Formato de email inválido';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO clients (name, phone, email, service_history) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $phone, $email, $service_history])) {
                $success = true;
                // Limpar formulário após sucesso
                $name = $phone = $email = $service_history = '';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Erro ao salvar cliente: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia - Adicionar Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'dashboard.php'; ?>

    <main class="p-6 ml-64">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Adicionar Novo Cliente</h2>
            <a href="client_list.php" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Voltar para Clientes
            </a>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                Cliente adicionado com sucesso!
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
                        <label class="block text-gray-700 mb-2" for="name">Nome Completo*</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required
                            class="w-full px-3 py-2 border <?php echo isset($errors['name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="phone">Telefone*</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required
                            class="w-full px-3 py-2 border <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['phone'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['phone']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>"
                            class="w-full px-3 py-2 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                        <?php if (isset($errors['email'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['email']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2" for="service_history">Histórico de Serviços</label>
                        <textarea id="service_history" name="service_history" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($service_history ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="reset" class="mr-3 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md">
                        Limpar
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md">
                        <i class="fas fa-save mr-1"></i> Salvar Cliente
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Formatação automática do telefone
        document.getElementById('phone').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>
</html>