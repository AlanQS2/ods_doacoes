<?php
require 'db.php';
verificarLogin();

if (!isset($_GET['tipo']) || !isset($_GET['id'])) {
    header("Location: feed.php");
    exit;
}

$tipo_item = $_GET['tipo'];
$item_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// --- 1. PROCESSAR ENVIO DA AVALIAÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... Lógica mantida ...
    $avaliado_id = $_POST['avaliado_id'];
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];

    if ($tipo_item == 'doacao') {
        $check = $pdo->prepare("SELECT id FROM reviews WHERE doacao_id = ? AND avaliador_id = ? AND avaliado_id = ?");
        $check->execute([$item_id, $user_id, $avaliado_id]);
        if ($check->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO reviews (doacao_id, avaliador_id, avaliado_id, nota, comentario) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$item_id, $user_id, $avaliado_id, $nota, $comentario]);
        }
    } 
    elseif ($tipo_item == 'refeicao') {
        $check = $pdo->prepare("SELECT id FROM meal_reviews WHERE refeicao_id = ? AND avaliador_id = ? AND avaliado_id = ?");
        $check->execute([$item_id, $user_id, $avaliado_id]);
        if ($check->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO meal_reviews (refeicao_id, avaliador_id, avaliado_id, nota, comentario) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$item_id, $user_id, $avaliado_id, $nota, $comentario]);
        }
    }
    header("Location: avaliar.php?tipo=$tipo_item&id=$item_id");
    exit;
}

// --- 2. BUSCAR DADOS (Lógica PHP mantida) ---
$dados_item = [];
$participantes = [];
$avaliados_ids = [];

// ... Blocos PHP de busca de participantes (mantidos iguais ao original) ...
if ($tipo_item == 'doacao') {
    $sql = "SELECT d.titulo, d.produtor_id, u_prod.nome as produtor_nome, d.distribuidor_id, u_dist.nome as distribuidor_nome, d.cozinheiro_id, u_coz.nome as cozinheiro_nome FROM donations d LEFT JOIN users u_prod ON d.produtor_id = u_prod.id LEFT JOIN users u_dist ON d.distribuidor_id = u_dist.id LEFT JOIN users u_coz ON d.cozinheiro_id = u_coz.id WHERE d.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_id]);
    $dados_item = $stmt->fetch();

    if ($dados_item) {
        if ($dados_item['produtor_id'] && $dados_item['produtor_id'] != $user_id) $participantes[] = ['id' => $dados_item['produtor_id'], 'nome' => $dados_item['produtor_nome'], 'papel' => 'Produtor'];
        if ($dados_item['distribuidor_id'] && $dados_item['distribuidor_id'] != $user_id) $participantes[] = ['id' => $dados_item['distribuidor_id'], 'nome' => $dados_item['distribuidor_nome'], 'papel' => 'Distribuidor'];
        if ($dados_item['cozinheiro_id'] && $dados_item['cozinheiro_id'] != $user_id) $participantes[] = ['id' => $dados_item['cozinheiro_id'], 'nome' => $dados_item['cozinheiro_nome'], 'papel' => 'Cozinheiro'];
        
        $stmtRev = $pdo->prepare("SELECT avaliado_id FROM reviews WHERE doacao_id = ? AND avaliador_id = ?");
        $stmtRev->execute([$item_id, $user_id]);
        while ($row = $stmtRev->fetch()) $avaliados_ids[] = $row['avaliado_id'];
    }
} elseif ($tipo_item == 'refeicao') {
    $sql = "SELECT m.titulo, m.cozinheiro_id, u_coz.nome as cozinheiro_nome, m.distribuidor_id, u_dist.nome as distribuidor_nome FROM meals m LEFT JOIN users u_coz ON m.cozinheiro_id = u_coz.id LEFT JOIN users u_dist ON m.distribuidor_id = u_dist.id WHERE m.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_id]);
    $dados_item = $stmt->fetch();
    if ($dados_item) {
        if ($dados_item['cozinheiro_id'] && $dados_item['cozinheiro_id'] != $user_id) $participantes[] = ['id' => $dados_item['cozinheiro_id'], 'nome' => $dados_item['cozinheiro_nome'], 'papel' => 'Cozinheiro'];
        if ($dados_item['distribuidor_id'] && $dados_item['distribuidor_id'] != $user_id) $participantes[] = ['id' => $dados_item['distribuidor_id'], 'nome' => $dados_item['distribuidor_nome'], 'papel' => 'Distribuidor'];
        
        $stmtRev = $pdo->prepare("SELECT avaliado_id FROM meal_reviews WHERE refeicao_id = ? AND avaliador_id = ?");
        $stmtRev->execute([$item_id, $user_id]);
        while ($row = $stmtRev->fetch()) $avaliados_ids[] = $row['avaliado_id'];
    }
}

if (!$dados_item) die("Item não encontrado.");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar - AlimentoSolidário</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Avaliação</h1>
                    <span class="text-xs uppercase tracking-wide text-gray-500 font-semibold bg-gray-100 px-2 py-1 rounded mt-1 inline-block">
                        <?= ucfirst($tipo_item) ?>
                    </span>
                </div>
                <a href="feed.php" class="text-green-600 hover:underline text-sm font-medium flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Voltar ao Feed
                </a>
            </div>
            <p class="text-gray-600 text-sm md:text-base">Referente a: <strong class="text-gray-900"><?= htmlspecialchars($dados_item['titulo']) ?></strong></p>
        </div>

        <div class="space-y-6">
            <?php foreach ($participantes as $p): ?>
                <?php $ja_avaliou = in_array($p['id'], $avaliados_ids); ?>
                
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 relative transition-all hover:shadow-md">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-sm shrink-0">
                                <?= strtoupper(substr($p['nome'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block"><?= $p['papel'] ?></span>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight"><?= htmlspecialchars($p['nome']) ?></h3>
                            </div>
                        </div>
                        <?php if ($ja_avaliou): ?>
                            <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 border border-green-100 self-start sm:self-auto">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Avaliado
                            </span>
                        <?php else: ?>
                            <span class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-100 self-start sm:self-auto">
                                Pendente
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$ja_avaliou): ?>
                        <form method="POST" class="mt-4 border-t border-gray-100 pt-4">
                            <input type="hidden" name="avaliado_id" value="<?= $p['id'] ?>">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Classifique sua experiência</label>
                                <div class="flex flex-wrap gap-4 justify-start">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="nota" value="<?= $i ?>" required class="peer sr-only">
                                            <svg class="w-8 h-8 md:w-10 md:h-10 text-gray-200 peer-checked:text-yellow-400 group-hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="block text-center text-xs text-gray-400 group-hover:text-yellow-600 mt-1"><?= $i ?></span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comentário (Opcional)</label>
                                <textarea name="comentario" rows="2" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm" placeholder="O que você achou da colaboração?"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-gray-900 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-gray-800 transition shadow-sm">
                                Enviar Avaliação
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                            <p class="text-gray-500 text-sm italic">Obrigado! Sua avaliação foi registrada com sucesso.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($participantes)): ?>
                <div class="text-center text-gray-500 py-10 bg-white rounded-xl border border-dashed">
                    Não há outros participantes para avaliar neste fluxo.
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>