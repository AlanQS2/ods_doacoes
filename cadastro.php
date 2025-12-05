<?php
require 'db.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação básica
    if (!isset($_POST['termos'])) {
        $erro = "Você deve aceitar os Termos de Uso para continuar."; // RB005
    } else {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['password'];
        $tipo = $_POST['tipo'];
        $cidade = trim($_POST['cidade']); // Essencial para RB001
        $estado = trim($_POST['estado']);
        $telefone = $_POST['telefone'];

        // Verifica duplicidade
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (nome, email, password_hash, telefone, cidade, estado, tipo, termos_aceitos) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nome, $email, $senha_hash, $telefone, $cidade, $estado, $tipo])) {
                $sucesso = "Cadastro realizado! Faça login.";
                header("refresh:2;url=login.php");
            } else {
                $erro = "Erro ao cadastrar.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - ODS 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Cadastro</h2>
        <?php if($erro): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $erro ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $sucesso ?></div><?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="nome" placeholder="Nome Completo" required class="w-full border p-2 rounded">
            <input type="email" name="email" placeholder="Email" required class="w-full border p-2 rounded">
            <input type="password" name="password" placeholder="Senha" required class="w-full border p-2 rounded">
            
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="cidade" placeholder="Cidade (Obrigatório)" required class="w-full border p-2 rounded">
                <input type="text" name="estado" placeholder="UF" maxlength="2" required class="w-full border p-2 rounded uppercase">
            </div>
            
            <input type="text" name="telefone" placeholder="Telefone" class="w-full border p-2 rounded">

            <select name="tipo" class="w-full border p-2 rounded">
                <option value="produtor">Produtor</option>
                <option value="distribuidor">Distribuidor</option>
                <option value="cozinheiro">Cozinheiro</option>
            </select>

            <div class="flex items-start gap-2 text-sm text-gray-600">
                <input type="checkbox" name="termos" id="termos" required class="mt-1">
                <label for="termos">Declaro que as informações são verdadeiras e aceito os <a href="#" class="text-green-600 underline">Termos de Uso</a> e o compromisso voluntário.</label>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded font-bold hover:bg-green-700">Cadastrar</button>
        </form>
    </div>
</body>
</html>