<?php
require 'db.php';
verificarLogin();

$user_id = $_SESSION['user_id'];
$msg = '';

// Atualizar Perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = $_POST['telefone'];
    $capacidade = $_POST['capacidade'] ?? null;
    $disponibilidade = $_POST['disponibilidade'] ?? null;

    $sql = "UPDATE users SET telefone = ?, capacidade_transporte = ?, capacidade_producao = ?, disponibilidade = ? WHERE id = ?";
    // Ajusta qual campo de capacidade salvar baseado no tipo
    $cap_transporte = ($_SESSION['user_tipo'] == 'distribuidor') ? $capacidade : null;
    $cap_producao = ($_SESSION['user_tipo'] == 'cozinheiro') ? $capacidade : null;

    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$telefone, $cap_transporte, $cap_producao, $disponibilidade, $user_id])) {
        $msg = "Perfil atualizado com sucesso!";
    }
}

// Buscar dados atuais + Nota Média (RF004)
$stmt = $pdo->prepare("SELECT u.*, 
    (SELECT AVG(nota) FROM reviews WHERE avaliado_id = u.id) as media_nota,
    (SELECT COUNT(*) FROM reviews WHERE avaliado_id = u.id) as total_reviews
    FROM users u WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// RB003: Alerta de pontuação baixa
$alerta_pontuacao = ($user['total_reviews'] >= 5 && $user['media_nota'] < 3);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - ODS 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b p-4 mb-8 flex justify-between container mx-auto">
        <span class="font-bold text-green-600">AlimentoSolidário</span>
        <a href="feed.php" class="text-gray-600 hover:text-green-600">Voltar ao Feed</a>
    </nav>

    <div class="container mx-auto px-4 max-w-2xl">
        
        <?php if($alerta_pontuacao): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Atenção!</p>
                <p>Sua nota média é inferior a 3 estrelas. Sua conta está sob revisão da administração.</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Gerenciar Perfil (<?= ucfirst($user['tipo']) ?>)</h1>
                <div class="text-right">
                    <span class="block text-sm text-gray-500">Avaliação Média</span>
                    <span class="text-yellow-500 font-bold text-xl">
                        ★ <?= number_format($user['media_nota'] ?: 0, 1) ?>
                    </span>
                    <span class="text-xs text-gray-400">/ 5 (<?= $user['total_reviews'] ?> avaliações)</span>
                </div>
            </div>

            <?php if($msg): ?><div class="bg-green-100 text-green-800 p-2 rounded mb-4"><?= $msg ?></div><?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" value="<?= htmlspecialchars($user['nome']) ?>" disabled class="w-full bg-gray-100 border p-2 rounded cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cidade/Estado</label>
                    <input type="text" value="<?= htmlspecialchars($user['cidade']) ?> - <?= $user['estado'] ?>" disabled class="w-full bg-gray-100 border p-2 rounded cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Para mudar de cidade, contate o suporte.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($user['telefone']) ?>" class="w-full border p-2 rounded">
                </div>

                <?php if($user['tipo'] == 'distribuidor'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacidade de Transporte (kg/vol)</label>
                        <input type="text" name="capacidade" value="<?= htmlspecialchars($user['capacidade_transporte']) ?>" class="w-full border p-2 rounded">
                    </div>
                <?php elseif($user['tipo'] == 'cozinheiro'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacidade de Produção (refeições/dia)</label>
                        <input type="text" name="capacidade" value="<?= htmlspecialchars($user['capacidade_producao']) ?>" class="w-full border p-2 rounded">
                    </div>
                <?php endif; ?>

                <?php if($user['tipo'] != 'produtor'): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Disponibilidade / Horários</label>
                        <textarea name="disponibilidade" rows="3" class="w-full border p-2 rounded"><?= htmlspecialchars($user['disponibilidade']) ?></textarea>
                    </div>
                <?php endif; ?>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>