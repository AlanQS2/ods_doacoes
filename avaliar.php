<?php
require 'db.php';
verificarLogin();

if (!isset($_GET['doacao'])) {
    header("Location: feed.php");
    exit;
}

$doacao_id = $_GET['doacao'];
$user_id = $_SESSION['user_id'];

// 1. Processar envio de UMA avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avaliado_id = $_POST['avaliado_id'];
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];

    // Verificar duplicidade
    $check = $pdo->prepare("SELECT id FROM reviews WHERE doacao_id = ? AND avaliador_id = ? AND avaliado_id = ?");
    $check->execute([$doacao_id, $user_id, $avaliado_id]);
    
    if ($check->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO reviews (doacao_id, avaliador_id, avaliado_id, nota, comentario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$doacao_id, $user_id, $avaliado_id, $nota, $comentario]);
    }
    // Recarrega a página para atualizar o status das avaliações
    header("Location: avaliar.php?doacao=" . $doacao_id);
    exit;
}

// 2. Buscar detalhes da doação e dos participantes
$sql = "SELECT d.titulo, 
               d.produtor_id, u_prod.nome as produtor_nome, u_prod.tipo as produtor_tipo,
               d.distribuidor_id, u_dist.nome as distribuidor_nome, u_dist.tipo as distribuidor_tipo,
               d.cozinheiro_id, u_coz.nome as cozinheiro_nome, u_coz.tipo as cozinheiro_tipo
        FROM donations d
        LEFT JOIN users u_prod ON d.produtor_id = u_prod.id
        LEFT JOIN users u_dist ON d.distribuidor_id = u_dist.id
        LEFT JOIN users u_coz ON d.cozinheiro_id = u_coz.id
        WHERE d.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$doacao_id]);
$dados = $stmt->fetch();

if (!$dados) {
    die("Doação não encontrada.");
}

// 3. Identificar quem eu devo avaliar (todos menos eu e nulos)
$participantes = [];

// Adiciona Produtor se não for eu
if ($dados['produtor_id'] && $dados['produtor_id'] != $user_id) {
    $participantes[] = ['id' => $dados['produtor_id'], 'nome' => $dados['produtor_nome'], 'tipo' => 'Produtor'];
}
// Adiciona Distribuidor se não for eu
if ($dados['distribuidor_id'] && $dados['distribuidor_id'] != $user_id) {
    $participantes[] = ['id' => $dados['distribuidor_id'], 'nome' => $dados['distribuidor_nome'], 'tipo' => 'Distribuidor'];
}
// Adiciona Cozinheiro se não for eu
if ($dados['cozinheiro_id'] && $dados['cozinheiro_id'] != $user_id) {
    $participantes[] = ['id' => $dados['cozinheiro_id'], 'nome' => $dados['cozinheiro_nome'], 'tipo' => 'Cozinheiro'];
}

// 4. Verificar quais eu JÁ avaliei
$avaliados_ids = [];
$stmtRev = $pdo->prepare("SELECT avaliado_id FROM reviews WHERE doacao_id = ? AND avaliador_id = ?");
$stmtRev->execute([$doacao_id, $user_id]);
while ($row = $stmtRev->fetch()) {
    $avaliados_ids[] = $row['avaliado_id'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Avaliar Participantes - ODS 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Avaliar Participantes</h1>
                <a href="feed.php" class="text-blue-600 hover:underline text-sm">Voltar ao Feed</a>
            </div>
            <p class="text-gray-600">Doação: <strong><?= htmlspecialchars($dados['titulo']) ?></strong></p>
            <p class="text-sm text-gray-500 mt-1">Sua opinião ajuda a manter a comunidade confiável.</p>
        </div>

        <div class="space-y-6">
            <?php foreach ($participantes as $p): ?>
                <?php $ja_avaliou = in_array($p['id'], $avaliados_ids); ?>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400"><?= $p['tipo'] ?></span>
                            <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($p['nome']) ?></h3>
                        </div>
                        <?php if ($ja_avaliou): ?>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                                ✓ Avaliado
                            </span>
                        <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">
                                Pendente
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$ja_avaliou): ?>
                        <form method="POST" class="mt-4 border-t pt-4">
                            <input type="hidden" name="avaliado_id" value="<?= $p['id'] ?>">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nota</label>
                                <div class="flex gap-4">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="nota" value="<?= $i ?>" required class="sr-only peer">
                                            <span class="text-3xl text-gray-300 peer-checked:text-yellow-400 group-hover:text-yellow-300 transition">★</span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comentário</label>
                                <textarea name="comentario" rows="2" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" placeholder="Ex: Rápido e atencioso..."></textarea>
                            </div>

                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-700 transition">
                                Enviar Avaliação para <?= htmlspecialchars($p['nome']) ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm italic">Obrigado por avaliar este parceiro!</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>