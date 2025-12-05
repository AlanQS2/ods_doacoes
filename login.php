<?php
require 'db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['password_hash'])) {
        // NOVA VERIFICAÇÃO: Checar se está banido
        if ($user['banned'] == 1) {
            $erro = "Esta conta foi suspensa pelos administradores.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo'];
            $_SESSION['user_cidade'] = $user['cidade'];
            
            // Redirecionamento inteligente
            if ($user['tipo'] == 'administrador') {
                header("Location: feed_admin.php");
            } else {
                header("Location: feed.php");
            }
            exit;
        }
    } else {
        $erro = "Email ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-green-50 to-white min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Bem-vindo de volta!</h2>
            <p class="text-gray-500">Acesse sua conta para continuar.</p>
        </div>

        <?php if($erro): ?>
            <div class="bg-red-50 border border-red-100 text-red-600 p-3 rounded-md text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <?= $erro ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 transition font-medium shadow-sm">
                Entrar
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm">
            <a href="index.php" class="text-gray-500 hover:text-gray-900 mr-4">← Voltar</a>
            <span class="text-gray-300">|</span>
            <a href="cadastro.php" class="text-green-600 font-bold hover:underline ml-4">Criar conta</a>
        </div>
    </div>
</body>
</html>