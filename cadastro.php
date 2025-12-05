<?php
require 'db.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação básica
    if (!isset($_POST['termos'])) {
        $erro = "Você deve aceitar os Termos de Uso para continuar."; 
    } else {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['password'];
        $tipo = $_POST['tipo'];
        // Agora recebemos diretamente do select
        $cidade = trim($_POST['cidade']); 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-100">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Criar Conta</h2>
        
        <?php if($erro): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm flex items-center gap-2">⚠️ <?= $erro ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm flex items-center gap-2">✅ <?= $sucesso ?></div><?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                <input type="text" name="nome" placeholder="Ex: Maria Silva" required class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" placeholder="seu@email.com" required class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input type="password" name="password" placeholder="******" required class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" id="estado" required class="w-full border border-gray-300 p-2.5 rounded bg-white focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
                        <option value="">UF</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <select name="cidade" id="cidade" required disabled class="w-full border border-gray-300 p-2.5 rounded bg-gray-50 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm disabled:cursor-not-allowed">
                        <option value="">Selecione o Estado...</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
                <input type="text" name="telefone" placeholder="(00) 00000-0000" class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Eu sou...</label>
                <select name="tipo" class="w-full border border-gray-300 p-2.5 rounded bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="produtor">Produtor (Quero doar alimentos)</option>
                    <option value="distribuidor">Distribuidor (Faço o transporte)</option>
                    <option value="cozinheiro">Cozinheiro (Preparo refeições)</option>
                </select>
            </div>

            <div class="flex items-start gap-2 text-sm text-gray-600 mt-2">
                <input type="checkbox" name="termos" id="termos" required class="mt-1 accent-green-600 w-4 h-4">
                <label for="termos" class="leading-tight cursor-pointer">Declaro que as informações são verdadeiras e aceito os <a href="#" class="text-green-600 font-bold hover:underline">Termos de Uso</a> e o compromisso voluntário.</label>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition-colors shadow-sm mt-4 text-base">
                Confirmar Cadastro
            </button>
            
            <div class="mt-6 text-center text-sm border-t pt-4 border-gray-100">
                <p class="text-gray-500">Já tem uma conta?</p>
                <a href="login.php" class="text-green-600 font-bold hover:underline">Fazer Login</a>
                <span class="mx-2 text-gray-300">|</span>
                <a href="index.php" class="text-gray-400 hover:text-gray-600">Voltar ao Início</a>
            </div>
        </form>
    </div>

    <script>
        const estadoSelect = document.getElementById('estado');
        const cidadeSelect = document.getElementById('cidade');

        // 1. Carregar Estados ao abrir a página
        fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome')
            .then(response => response.json())
            .then(estados => {
                estados.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.sigla; // Salva a UF (ex: SP) no banco
                    option.textContent = estado.sigla;
                    estadoSelect.appendChild(option);
                });
            });

        // 2. Carregar Cidades quando mudar o Estado
        estadoSelect.addEventListener('change', function() {
            const uf = this.value;
            
            // Limpar e desabilitar cidade enquanto carrega
            cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
            cidadeSelect.disabled = true;

            if (uf) {
                fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`)
                    .then(response => response.json())
                    .then(cidades => {
                        cidadeSelect.innerHTML = '<option value="">Selecione a Cidade</option>';
                        cidades.forEach(cidade => {
                            const option = document.createElement('option');
                            option.value = cidade.nome; // Salva o nome da cidade
                            option.textContent = cidade.nome;
                            cidadeSelect.appendChild(option);
                        });
                        cidadeSelect.disabled = false;
                        cidadeSelect.classList.remove('bg-gray-50');
                        cidadeSelect.classList.add('bg-white');
                    });
            } else {
                cidadeSelect.innerHTML = '<option value="">Selecione o Estado...</option>';
                cidadeSelect.disabled = true;
                cidadeSelect.classList.add('bg-gray-50');
            }
        });
    </script>
</body>
</html>