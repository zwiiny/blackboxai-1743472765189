<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validação
    if (empty($username)) {
        $errors['username'] = 'Nome de usuário é obrigatório';
    } elseif (strlen($username) < 4) {
        $errors['username'] = 'Nome de usuário deve ter pelo menos 4 caracteres';
    }

    if (empty($email)) {
        $errors['email'] = 'Email é obrigatório';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Por favor insira um email válido';
    }

    if (empty($password)) {
        $errors['password'] = 'Senha é obrigatória';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Senha deve ter pelo menos 6 caracteres';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'As senhas não coincidem';
    }

    // Verificar se usuário/email já existe
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors['general'] = 'Nome de usuário ou email já existem';
        }
    }

    // Criar usuário se não houver erros
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            $_SESSION['success_message'] = 'Cadastro realizado com sucesso! Por favor faça login.';
            header('Location: index.php');
            exit;
        } else {
            $errors['general'] = 'Falha no cadastro. Por favor tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia - Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Criar uma Conta</h1>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="username">Nome de Usuário</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required
                        class="w-full px-3 py-2 border <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                    <?php if (isset($errors['username'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['username']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required
                        class="w-full px-3 py-2 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                    <?php if (isset($errors['email'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['email']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="password">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="confirm_password">Confirmar Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="w-full px-3 py-2 border <?php echo isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                    Cadastrar
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="index.php" class="text-blue-500 hover:underline">Já tem uma conta? Faça login</a>
            </div>
        </div>
    </div>
</body>
</html>