<?php
require 'db.php';
verificarLogin();

$user_id = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['user_tipo'];
$cidade_usuario = $_SESSION['user_cidade'];

// Buscar lista de cozinheiros para modal (apenas da mesma cidade)
$cozinheiros = [];
if ($tipo_usuario == 'distribuidor') {
    $stmtCoz = $pdo->prepare("SELECT id, nome, cidade FROM users WHERE tipo = 'cozinheiro' AND cidade = ?");
    $stmtCoz->execute([$cidade_usuario]);
    $cozinheiros = $stmtCoz->fetchAll();
}

$termo_busca = $_GET['q'] ?? '';

// --- CONFIGURAÇÃO DA QUERY ---

// Subquery: Verifica se EU já avaliei esta doação
// Usamos :uid aqui. Se usarmos :uid novamente no WHERE, o db.php (emulate=true) resolve.
$sql_check_avaliacao = ", (SELECT COUNT(*) FROM reviews r WHERE r.doacao_id = d.id AND r.avaliador_id = :uid) as ja_avaliou";

$sql = "SELECT d.*, u.nome as produtor_nome, 
        (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
        (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
        $sql_check_avaliacao
        FROM donations d 
        JOIN users u ON d.produtor_id = u.id 
        WHERE u.cidade = :cidade";

// --- FILTROS DE VISUALIZAÇÃO ---

if ($tipo_usuario == 'produtor') {
    // Produtor vê suas doações
    $sql = "SELECT d.*, u.nome as produtor_nome, 
            (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
            (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
            $sql_check_avaliacao
            FROM donations d JOIN users u ON d.produtor_id = u.id 
            WHERE d.produtor_id = :uid";
    
    // Parâmetro :uid é usado 2 vezes na query (subquery + where). Emulate=true resolve.
    $params = ['uid' => $user_id];

} else {
    // Distribuidor e Cozinheiro
    
    // Base de parâmetros
    $params = ['cidade' => $cidade_usuario, 'uid' => $user_id];

    // Filtros de busca textual
    if ($termo_busca) {
        $sql .= " AND (d.titulo LIKE :busca OR d.descricao LIKE :busca)";
        $params['busca'] = "%$termo_busca%";
    }
    
    if ($tipo_usuario == 'distribuidor') {
        // Distribuidor vê: Disponíveis, Coletadas, Aguardando, Entregue (Histórico)
        $sql .= " AND (
                    d.status = 'disponivel' 
                    OR (
                        (d.status = 'coletada' OR d.status = 'aguardando_aceite' OR d.status = 'entregue') 
                        AND d.distribuidor_id = :uid_filter
                    )
                  )";
        // Usamos um nome diferente :uid_filter para evitar conflito lógico, embora PDO aceite repetição
        $params['uid_filter'] = $user_id;

    } elseif ($tipo_usuario == 'cozinheiro') {
        // Cozinheiro vê: Disponíveis, Entregues, Aguardando
        $sql .= " AND (
                    d.status = 'disponivel' 
                    OR (
                        (d.status = 'entregue' OR d.status = 'aguardando_aceite') 
                        AND d.cozinheiro_id = :uid_filter
                    )
                  )";
        $params['uid_filter'] = $user_id;
    }
}

$sql .= " ORDER BY FIELD(d.status, 'aguardando_aceite', 'coletada', 'disponivel', 'entregue'), d.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $doacoes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar feed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Feed - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal(doacaoId) {
            const modal = document.getElementById('modal-' + doacaoId);
            if (modal) modal.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b sticky top-0 z-50 px-4 py-3 shadow-sm flex justify-between items-center">
        <div class="font-bold text-green-600 text-xl">AlimentoSolidário</div>
        <div class="flex gap-4 items-center">
            <span class="text-sm text-gray-500 hidden sm:inline">📍 <?= htmlspecialchars($cidade_usuario) ?></span>
            <a href="perfil.php" class="text-sm font-medium hover:underline">Meu Perfil</a>
            <a href="logout.php" class="text-red-500 text-sm">Sair</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-5xl">
        
        <div class="flex justify-between items-center mb-8 gap-4">
            <form class="flex gap-2 flex-1">
                <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($termo_busca) ?>" class="flex-1 border p-2 rounded shadow-sm">
                <button class="bg-green-600 text-white px-4 rounded hover:bg-green-700">Buscar</button>
            </form>
            <?php if($tipo_usuario == 'produtor'): ?>
                <a href="criar_doacao.php" class="bg-green-600 text-white px-4 py-2 rounded shadow-sm hover:bg-green-700 font-bold text-sm">+ Doar</a>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($doacoes as $d): ?>
                <div class="bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
                    <div class="h-40 bg-gray-200 relative">
                        <img src="https://source.unsplash.com/random/400x300/?food,vegetable&sig=<?= $d['id'] ?>" class="w-full h-full object-cover">
                        <?php
                            $badgeColor = 'bg-gray-100 text-gray-800';
                            $statusLabel = strtoupper($d['status']);
                            if ($d['status'] == 'disponivel') $badgeColor = 'bg-green-100 text-green-800';
                            elseif ($d['status'] == 'coletada') $badgeColor = 'bg-blue-100 text-blue-800';
                            elseif ($d['status'] == 'aguardando_aceite') { $badgeColor = 'bg-yellow-100 text-yellow-800'; $statusLabel = "AGUARDANDO"; }
                            elseif ($d['status'] == 'entregue') { $badgeColor = 'bg-gray-800 text-white'; $statusLabel = "CONCLUÍDO"; }
                        ?>
                        <span class="absolute top-2 right-2 px-2 py-1 rounded text-xs font-bold shadow-sm <?= $badgeColor ?>">
                            <?= $statusLabel ?>
                        </span>
                    </div>
                    
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-lg mb-1 text-gray-800"><?= htmlspecialchars($d['titulo']) ?></h3>
                        <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-2"><?= htmlspecialchars($d['descricao']) ?></p>
                        
                        <div class="text-xs text-gray-500 border-t pt-3 space-y-1 mb-3">
                            <p>🧑‍🌾 Produtor: <span class="font-medium"><?= htmlspecialchars($d['produtor_nome']) ?></span></p>
                            <?php if($d['distribuidor_nome']): ?>
                                <p>🚚 Transporte: <span class="font-medium"><?= htmlspecialchars($d['distribuidor_nome']) ?></span></p>
                            <?php endif; ?>
                            <?php if($d['cozinheiro_nome']): ?>
                                <p>👨‍🍳 Destino: <span class="font-medium"><?= htmlspecialchars($d['cozinheiro_nome']) ?></span></p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <?php if($tipo_usuario == 'distribuidor'): ?>
                                <?php if ($d['status'] == 'disponivel'): ?>
                                    <form action="processar_acao.php" method="POST">
                                        <input type="hidden" name="acao" value="coletar">
                                        <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                        <button class="w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700">🚚 Coletar</button>
                                    </form>
                                <?php elseif ($d['status'] == 'coletada' && $d['distribuidor_id'] == $user_id): ?>
                                    <button onclick="toggleModal(<?= $d['id'] ?>)" class="w-full bg-orange-500 text-white py-2 rounded text-sm hover:bg-orange-600">👨‍🍳 Escolher Destino</button>
                                    <div id="modal-<?= $d['id'] ?>" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
                                        <div class="bg-white rounded-lg p-6 w-full max-w-sm">
                                            <h3 class="font-bold mb-4">Entregar para:</h3>
                                            <form action="processar_acao.php" method="POST">
                                                <input type="hidden" name="acao" value="entregar">
                                                <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                <select name="cozinheiro_id" required class="w-full border p-2 rounded mb-4 text-sm">
                                                    <option value="">Selecione...</option>
                                                    <?php foreach($cozinheiros as $coz): ?>
                                                        <option value="<?= $coz['id'] ?>"><?= htmlspecialchars($coz['nome']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="flex gap-2 justify-end">
                                                    <button type="button" onclick="toggleModal(<?= $d['id'] ?>)" class="px-3 py-1 text-gray-500">Cancelar</button>
                                                    <button class="bg-orange-500 text-white px-3 py-1 rounded">Enviar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php elseif ($d['status'] == 'aguardando_aceite'): ?>
                                    <div class="text-center text-xs text-yellow-600 bg-yellow-50 py-2 rounded border border-yellow-100">⏳ Aguardando <?= htmlspecialchars($d['cozinheiro_nome']) ?></div>
                                <?php elseif ($d['status'] == 'entregue'): ?>
                                    <?php if(!$d['ja_avaliou']): ?>
                                        <a href="avaliar.php?doacao=<?= $d['id'] ?>&alvo=<?= $d['cozinheiro_id'] ?>" class="block text-center w-full border border-yellow-400 text-yellow-600 py-2 rounded text-sm hover:bg-yellow-50">★ Avaliar Cozinheiro</a>
                                    <?php else: ?>
                                        <div class="text-center text-green-600 text-sm font-medium">Avaliação Enviada ✓</div>
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php elseif($tipo_usuario == 'cozinheiro'): ?>
                                <?php if ($d['status'] == 'aguardando_aceite'): ?>
                                    <div class="flex gap-2">
                                        <form action="processar_acao.php" method="POST" class="flex-1">
                                            <input type="hidden" name="acao" value="aceitar_entrega">
                                            <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                            <button class="w-full bg-green-600 text-white py-2 rounded text-xs">Aceitar</button>
                                        </form>
                                        <form action="processar_acao.php" method="POST" class="flex-1">
                                            <input type="hidden" name="acao" value="recusar_entrega">
                                            <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                            <button class="w-full bg-red-100 text-red-600 py-2 rounded text-xs hover:bg-red-200">Recusar</button>
                                        </form>
                                    </div>
                                <?php elseif($d['status'] == 'entregue'): ?>
                                    <div class="text-center text-gray-500 text-sm bg-gray-100 py-2 rounded">Recebido ✓</div>
                                <?php elseif($d['status'] == 'disponivel'): ?>
                                    <span class="text-xs text-gray-400 text-center block">Disponível no mercado</span>
                                <?php endif; ?>

                            <?php elseif($tipo_usuario == 'produtor'): ?>
                                <?php if($d['status'] == 'entregue'): ?>
                                    <?php if(!$d['ja_avaliou'] && $d['distribuidor_id']): ?>
                                        <a href="avaliar.php?doacao=<?= $d['id'] ?>&alvo=<?= $d['distribuidor_id'] ?>" class="block text-center w-full border border-yellow-400 text-yellow-600 py-2 rounded text-sm hover:bg-yellow-50">★ Avaliar Distribuidor</a>
                                    <?php elseif($d['ja_avaliou']): ?>
                                        <div class="text-center text-green-600 text-sm font-medium">Avaliação Enviada ✓</div>
                                    <?php else: ?>
                                        <div class="text-center text-gray-400 text-xs">Em andamento...</div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>