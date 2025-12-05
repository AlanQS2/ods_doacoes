<?php
require 'db.php';
verificarLogin();

$user_id = $_SESSION['user_id'];
$msg = '';

// Atualizar Perfil (Lógica mantida)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = $_POST['telefone'];
    $capacidade = $_POST['capacidade'] ?? null;
    $disponibilidade = $_POST['disponibilidade'] ?? null;

    $sql = "UPDATE users SET telefone = ?, capacidade_transporte = ?, capacidade_producao = ?, disponibilidade = ? WHERE id = ?";
    $cap_transporte = ($_SESSION['user_tipo'] == 'distribuidor') ? $capacidade : null;
    $cap_producao = ($_SESSION['user_tipo'] == 'cozinheiro') ? $capacidade : null;

    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$telefone, $cap_transporte, $cap_producao, $disponibilidade, $user_id])) {
        $msg = "Perfil atualizado com sucesso!";
    }
}

// Buscar dados atuais + Nota Média
$stmt = $pdo->prepare("SELECT u.*, 
    (SELECT AVG(nota) FROM reviews WHERE avaliado_id = u.id) as media_nota,
    (SELECT COUNT(*) FROM reviews WHERE avaliado_id = u.id) as total_reviews
    FROM users u WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$alerta_pontuacao = ($user['total_reviews'] >= 5 && $user['media_nota'] < 3);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Meu Perfil - ODS 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white border-b p-4 mb-6">
        <div class="container mx-auto max-w-2xl flex justify-between items-center">
            <span class="font-bold text-green-600 text-lg">AlimentoSolidário</span>
            <a href="feed.php" class="text-gray-600 hover:text-green-600 text-sm flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Voltar
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 max-w-2xl pb-10">
        
        <?php if($alerta_pontuacao): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
                <p class="font-bold">Atenção!</p>
                <p class="text-sm">Sua nota média é inferior a 3 estrelas. Sua conta está sob revisão.</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-gray-100 pb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gerenciar Perfil</h1>
                    <span class="text-sm text-gray-500 capitalize bg-gray-100 px-2 py-0.5 rounded mt-1 inline-block"><?= $user['tipo'] ?></span>
                </div>
                
                <div class="flex items-center gap-3 bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-100 w-full md:w-auto justify-between md:justify-start">
                    <span class="text-sm text-yellow-800 font-medium">Avaliação</span>
                    <div class="text-right">
                        <span class="block text-yellow-600 font-bold text-lg leading-none">
                            ★ <?= number_format($user['media_nota'] ?: 0, 1) ?>
                        </span>
                        <span class="text-[10px] text-yellow-700 block mt-0.5"><?= $user['total_reviews'] ?> avaliações</span>
                    </div>
                </div>
            </div>

            <?php if($msg): ?><div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm flex items-center gap-2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><?= $msg ?></div><?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" value="<?= htmlspecialchars($user['nome']) ?>" disabled class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded text-gray-500 cursor-not-allowed text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade/Estado</label>
                    <input type="text" value="<?= htmlspecialchars($user['cidade']) ?> - <?= $user['estado'] ?>" disabled class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded text-gray-500 cursor-not-allowed text-sm">
                    <p class="text-xs text-gray-400 mt-1">Para mudar de cidade, contate o suporte.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($user['telefone']) ?>" class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none transition-shadow">
                </div>

                <?php if($user['tipo'] == 'distribuidor'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidade de Transporte (kg/vol)</label>
                        <input type="text" name="capacidade" value="<?= htmlspecialchars($user['capacidade_transporte']) ?>" class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none transition-shadow">
                    </div>
                <?php elseif($user['tipo'] == 'cozinheiro'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidade de Produção (refeições/dia)</label>
                        <input type="text" name="capacidade" value="<?= htmlspecialchars($user['capacidade_producao']) ?>" class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-orange-500 focus:outline-none transition-shadow">
                    </div>
                <?php endif; ?>

                <?php if($user['tipo'] != 'produtor'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidade / Horários</label>
                        <textarea name="disponibilidade" rows="3" class="w-full border border-gray-300 p-2.5 rounded focus:ring-2 focus:ring-green-500 focus:outline-none transition-shadow text-sm"><?= htmlspecialchars($user['disponibilidade']) ?></textarea>
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm mt-4">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>