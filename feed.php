<?php
require 'db.php';
verificarLogin();

$user_id = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['user_tipo'];
$cidade_usuario = $_SESSION['user_cidade'];
$nome_usuario = $_SESSION['user_nome'];

// Buscar lista de cozinheiros para modal (apenas da mesma cidade)
$cozinheiros = [];
if ($tipo_usuario == 'distribuidor') {
    $stmtCoz = $pdo->prepare("SELECT id, nome, cidade FROM users WHERE tipo = 'cozinheiro' AND cidade = ?");
    $stmtCoz->execute([$cidade_usuario]);
    $cozinheiros = $stmtCoz->fetchAll();
}

$termo_busca = $_GET['q'] ?? '';

// --- CONFIGURAÇÃO DA QUERY ---

// Conta quantas avaliações EU já fiz para essa doação
$sql_check_avaliacao = ", (SELECT COUNT(*) FROM reviews r WHERE r.doacao_id = d.id AND r.avaliador_id = :uid_avaliacao) as qtd_avaliacoes";

$sql = "SELECT d.*, u.nome as produtor_nome, u.cidade, 
        (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
        (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
        $sql_check_avaliacao
        FROM donations d 
        JOIN users u ON d.produtor_id = u.id 
        WHERE u.cidade = :cidade_filtro";

$params = [];

// Variáveis para estatísticas da sidebar
$stats_ativas = 0;
$stats_total = 0;

if ($tipo_usuario == 'produtor') {
    $sql = "SELECT d.*, u.nome as produtor_nome, u.cidade, 
            (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
            (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
            $sql_check_avaliacao
            FROM donations d JOIN users u ON d.produtor_id = u.id 
            WHERE d.produtor_id = :uid_produtor";
    
    $params = [
        'uid_avaliacao' => $user_id,
        'uid_produtor' => $user_id
    ];

} else {
    $params = [
        'uid_avaliacao' => $user_id,
        'cidade_filtro' => $cidade_usuario
    ];

    if ($termo_busca) {
        $sql .= " AND (d.titulo LIKE :busca_titulo OR d.descricao LIKE :busca_desc)";
        $params['busca_titulo'] = "%$termo_busca%";
        $params['busca_desc']   = "%$termo_busca%";
    }
    
    if ($tipo_usuario == 'distribuidor') {
        $sql .= " AND (
                    d.status = 'disponivel' 
                    OR (
                        (d.status = 'coletada' OR d.status = 'aguardando_aceite' OR d.status = 'entregue') 
                        AND d.distribuidor_id = :uid_distribuidor
                    )
                  )";
        $params['uid_distribuidor'] = $user_id;

    } elseif ($tipo_usuario == 'cozinheiro') {
        $sql .= " AND (
                    d.status = 'disponivel' 
                    OR (
                        (d.status = 'entregue' OR d.status = 'aguardando_aceite') 
                        AND d.cozinheiro_id = :uid_cozinheiro
                    )
                  )";
        $params['uid_cozinheiro'] = $user_id;
    }
}

$sql .= " ORDER BY FIELD(d.status, 'aguardando_aceite', 'coletada', 'disponivel', 'entregue'), d.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $doacoes = $stmt->fetchAll();

    // Calcular estatísticas simples para a sidebar
    $stats_total = count($doacoes);
    foreach($doacoes as $d) {
        if ($d['status'] == 'disponivel' || $d['status'] == 'coletada') {
            $stats_ativas++;
        }
    }

} catch (PDOException $e) {
    die("Erro ao carregar feed: " . $e->getMessage());
}

// Função auxiliar para calcular dias restantes
function diasRestantes($data_limite) {
    $hoje = new DateTime();
    $limite = new DateTime($data_limite);
    $intervalo = $hoje->diff($limite);
    $dias = $intervalo->format('%r%a'); // %r inclui o sinal negativo se venceu
    
    if ($dias < 0) return "Vencido";
    if ($dias == 0) return "Vence hoje";
    return "-" . $dias . " dias";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>AlimentoSolidário</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        function toggleModal(doacaoId) {
            const modal = document.getElementById('modal-' + doacaoId);
            if (modal) modal.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-[#F8FAFC]"> <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="index.php" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Voltar
                </a>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    <span class="text-lg font-semibold text-gray-900">AlimentoSolidário</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="hidden md:flex flex-col items-end mr-2">
                    <span class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($nome_usuario) ?></span>
                    <span class="text-xs text-gray-500 capitalize">(<?= $tipo_usuario ?>)</span>
                </div>
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm">
                    <?= strtoupper(substr($nome_usuario, 0, 2)) ?>
                </div>
                <a href="logout.php" class="text-gray-400 hover:text-red-500 ml-2" title="Sair">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-green-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900"><?= htmlspecialchars($nome_usuario) ?></h2>
                            <p class="text-sm text-gray-500 capitalize"><?= $tipo_usuario ?> • <?= htmlspecialchars($cidade_usuario) ?></p>
                        </div>
                    </div>

                    <?php if($tipo_usuario == 'produtor'): ?>
                        <a href="criar_doacao.php" class="flex items-center justify-center w-full bg-[#10B981] hover:bg-[#059669] text-white font-medium py-2.5 rounded-lg transition-colors mb-6 shadow-sm">
                            <span class="mr-2 text-lg">+</span> Nova Doação
                        </a>
                    <?php endif; ?>

                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Estatísticas</h3>
                        <div class="flex justify-between items-center mb-2 text-sm">
                            <span class="text-gray-500">Doações ativas</span>
                            <span class="font-medium text-gray-900"><?= $stats_ativas ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Total listado</span>
                            <span class="font-medium text-gray-900"><?= $stats_total ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="perfil.php" class="text-sm text-gray-500 hover:text-green-600 font-medium">Editar Perfil →</a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php 
                            if($tipo_usuario == 'produtor') echo "Minhas Doações";
                            elseif($tipo_usuario == 'distribuidor') echo "Feed de Coletas";
                            else echo "Ingredientes Disponíveis";
                        ?>
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Gerencie e visualize as doações de alimentos.</p>
                </div>

                <form class="mb-6 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="q" value="<?= htmlspecialchars($termo_busca) ?>" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Buscar por título ou descrição...">
                </form>

                <?php if(empty($doacoes)): ?>
                    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-300 mb-4">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Nenhuma doação encontrada</h3>
                        <p class="mt-1 text-gray-500">Tente ajustar seus filtros de busca.</p>
                    </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <?php foreach($doacoes as $d): ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow duration-200">
                            
                            <div class="w-full md:w-48 h-48 md:h-auto relative flex-shrink-0">
                                <img src="https://source.unsplash.com/random/400x400/?<?= $d['tipo'] == 'frutas' ? 'fruit' : 'vegetable' ?>&sig=<?= $d['id'] ?>" 
                                     class="w-full h-full object-cover" 
                                     alt="Imagem do alimento">
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($d['titulo']) ?></h3>
                                            
                                            <?php
                                                $statusClass = 'bg-gray-100 text-gray-700';
                                                $statusText = $d['status'];
                                                if($d['status'] == 'disponivel') { $statusClass = 'bg-gray-900 text-white'; $statusText = 'Disponível'; }
                                                elseif($d['status'] == 'entregue') { $statusClass = 'bg-green-100 text-green-800'; $statusText = 'Entregue'; }
                                                elseif($d['status'] == 'aguardando_aceite') { $statusClass = 'bg-yellow-100 text-yellow-800'; $statusText = 'Aguardando'; }
                                                elseif($d['status'] == 'coletada') { $statusClass = 'bg-blue-100 text-blue-800'; $statusText = 'Coletada'; }
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                <?= htmlspecialchars($d['produtor_nome']) ?>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                <?= htmlspecialchars($d['cidade']) ?>
                                            </span>
                                            <span class="flex items-center gap-1 font-medium text-gray-700">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                                <?= htmlspecialchars($d['quantidade']) ?> <?= htmlspecialchars($d['unidade']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if($d['status'] == 'disponivel'): ?>
                                        <span class="flex items-center gap-1 px-2 py-1 rounded text-xs font-bold bg-[#DC2626] text-white">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <?= diasRestantes($d['data_limite']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-gray-600 text-sm mt-2 mb-4 line-clamp-2">
                                    <?= htmlspecialchars($d['descricao']) ?>
                                </p>

                                <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4">
                                    <div class="flex gap-4 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Colheita: <?= date('d/m/Y', strtotime($d['data_colheita'])) ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Limite: <?= date('d/m/Y', strtotime($d['data_limite'])) ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center">
                                        <?php if($tipo_usuario == 'distribuidor' && $d['status'] == 'disponivel'): ?>
                                            <form action="processar_acao.php" method="POST">
                                                <input type="hidden" name="acao" value="coletar">
                                                <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                <button class="text-sm font-medium text-blue-600 hover:text-blue-800 transition">Coletar agora</button>
                                            </form>
                                        
                                        <?php elseif($d['status'] == 'coletada' && $d['distribuidor_id'] == $user_id && $tipo_usuario == 'distribuidor'): ?>
                                            <button onclick="toggleModal(<?= $d['id'] ?>)" class="text-sm font-medium text-orange-600 hover:text-orange-800 transition">Entregar</button>
                                            <div id="modal-<?= $d['id'] ?>" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
                                                <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
                                                    <h3 class="font-bold mb-4 text-gray-900">Selecionar Cozinheiro</h3>
                                                    <form action="processar_acao.php" method="POST">
                                                        <input type="hidden" name="acao" value="entregar">
                                                        <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                        <select name="cozinheiro_id" required class="w-full border border-gray-300 rounded-lg p-2.5 mb-4 text-sm bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                                                            <option value="">Selecione...</option>
                                                            <?php foreach($cozinheiros as $coz): ?>
                                                                <option value="<?= $coz['id'] ?>"><?= htmlspecialchars($coz['nome']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="flex gap-2 justify-end">
                                                            <button type="button" onclick="toggleModal(<?= $d['id'] ?>)" class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg text-sm font-medium">Cancelar</button>
                                                            <button class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700">Confirmar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        <?php elseif($d['status'] == 'aguardando_aceite' && $tipo_usuario == 'cozinheiro'): ?>
                                            <div class="flex gap-3">
                                                <form action="processar_acao.php" method="POST">
                                                    <input type="hidden" name="acao" value="aceitar_entrega">
                                                    <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                    <button class="text-sm font-medium text-green-600 hover:text-green-800">Aceitar</button>
                                                </form>
                                                <form action="processar_acao.php" method="POST">
                                                    <input type="hidden" name="acao" value="recusar_entrega">
                                                    <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                    <button class="text-sm font-medium text-red-600 hover:text-red-800">Recusar</button>
                                                </form>
                                            </div>

                                        <?php elseif($d['status'] == 'entregue'): ?>
                                            <a href="avaliar.php?doacao=<?= $d['id'] ?>" class="text-sm font-medium text-yellow-600 hover:text-yellow-800 flex items-center gap-1">
                                                <span>★</span> Avaliar Participantes
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>