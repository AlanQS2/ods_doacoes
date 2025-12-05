<?php
require 'db.php';
verificarLogin();

$user_id = $_SESSION['user_id'];
$tipo_usuario = $_SESSION['user_tipo'];
$cidade_usuario = $_SESSION['user_cidade'];
$nome_usuario = $_SESSION['user_nome'];

// Buscar cozinheiros (para modal de entrega de doação)
$cozinheiros = [];
if ($tipo_usuario == 'distribuidor') {
    $stmtCoz = $pdo->prepare("SELECT id, nome, cidade FROM users WHERE tipo = 'cozinheiro' AND cidade = ?");
    $stmtCoz->execute([$cidade_usuario]);
    $cozinheiros = $stmtCoz->fetchAll();
}

$termo_busca = $_GET['q'] ?? '';

// --- 1. CONSULTA DE DOAÇÕES ---
// CORREÇÃO 1: O nome do campo aqui é 'ja_avaliou'
$sql_check_avaliacao_doacao = ", (SELECT COUNT(*) FROM reviews r WHERE r.doacao_id = d.id AND r.avaliador_id = :uid_av_d) as ja_avaliou";

// CORREÇÃO 2: Adicionado 'u.cidade' na seleção para evitar o erro de cidade indefinida
$sql_doacao = "SELECT d.*, u.nome as produtor_nome, u.cidade,
        (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
        (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
        $sql_check_avaliacao_doacao
        FROM donations d JOIN users u ON d.produtor_id = u.id WHERE u.cidade = :cidade_d";

$params_d = ['uid_av_d' => $user_id, 'cidade_d' => $cidade_usuario];

if ($tipo_usuario == 'produtor') {
    $sql_doacao = "SELECT d.*, u.nome as produtor_nome, u.cidade,
            (SELECT nome FROM users WHERE id = d.distribuidor_id) as distribuidor_nome,
            (SELECT nome FROM users WHERE id = d.cozinheiro_id) as cozinheiro_nome
            $sql_check_avaliacao_doacao
            FROM donations d JOIN users u ON d.produtor_id = u.id WHERE d.produtor_id = :uid_d";
    $params_d = ['uid_av_d' => $user_id, 'uid_d' => $user_id];
} elseif ($tipo_usuario == 'distribuidor') {
    $sql_doacao .= " AND (d.status = 'disponivel' OR ((d.status = 'coletada' OR d.status = 'aguardando_aceite' OR d.status = 'entregue') AND d.distribuidor_id = :uid_d))";
    $params_d['uid_d'] = $user_id;
} elseif ($tipo_usuario == 'cozinheiro') {
    $sql_doacao .= " AND (d.status = 'disponivel' OR ((d.status = 'entregue' OR d.status = 'aguardando_aceite') AND d.cozinheiro_id = :uid_d))";
    $params_d['uid_d'] = $user_id;
}
$sql_doacao .= " ORDER BY d.created_at DESC";
$stmt_d = $pdo->prepare($sql_doacao);
$stmt_d->execute($params_d);
$doacoes = $stmt_d->fetchAll();


// --- 2. CONSULTA DE REFEIÇÕES ---
$refeicoes = [];
if ($tipo_usuario != 'produtor') { 
    
    $sql_check_avaliacao_ref = ", (SELECT COUNT(*) FROM meal_reviews r WHERE r.refeicao_id = m.id AND r.avaliador_id = :uid_av_m) as ja_avaliou";

    // CORREÇÃO 2: Adicionado 'u.cidade' aqui também para o cozinheiro da refeição
    $sql_refeicao = "SELECT m.*, u.nome as cozinheiro_nome, u.cidade,
            (SELECT nome FROM users WHERE id = m.distribuidor_id) as distribuidor_nome
            $sql_check_avaliacao_ref
            FROM meals m JOIN users u ON m.cozinheiro_id = u.id WHERE u.cidade = :cidade_m";
    
    $params_m = ['uid_av_m' => $user_id, 'cidade_m' => $cidade_usuario];

    if ($tipo_usuario == 'cozinheiro') {
        $sql_refeicao = "SELECT m.*, u.nome as cozinheiro_nome, u.cidade,
            (SELECT nome FROM users WHERE id = m.distribuidor_id) as distribuidor_nome
            $sql_check_avaliacao_ref
            FROM meals m JOIN users u ON m.cozinheiro_id = u.id WHERE m.cozinheiro_id = :uid_m";
        $params_m = ['uid_av_m' => $user_id, 'uid_m' => $user_id];
    } elseif ($tipo_usuario == 'distribuidor') {
        $sql_refeicao .= " AND (m.status = 'disponivel' OR m.distribuidor_id = :uid_m)";
        $params_m['uid_m'] = $user_id;
    }

    $sql_refeicao .= " ORDER BY m.created_at DESC";
    $stmt_m = $pdo->prepare($sql_refeicao);
    $stmt_m->execute($params_m);
    $refeicoes = $stmt_m->fetchAll();
}

function diasRestantes($data_limite) {
    $hoje = new DateTime();
    $limite = new DateTime($data_limite);
    $intervalo = $hoje->diff($limite);
    $dias = $intervalo->format('%r%a');
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-[#F8FAFC]">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    <span class="text-lg font-semibold text-gray-900">AlimentoSolidário</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <span class="block text-sm font-semibold text-gray-900"><?= htmlspecialchars($nome_usuario) ?></span>
                    <span class="block text-xs text-gray-500 capitalize"><?= $tipo_usuario ?></span>
                </div>
                <a href="logout.php" class="text-red-500 hover:text-red-700 ml-2">Sair</a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-green-50 flex items-center justify-center font-bold text-green-600 text-xl">
                            <?= strtoupper(substr($nome_usuario, 0, 1)) ?>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900"><?= htmlspecialchars($nome_usuario) ?></h2>
                            <p class="text-sm text-gray-500 capitalize"><?= $cidade_usuario ?></p>
                        </div>
                    </div>

                    <?php if($tipo_usuario == 'produtor'): ?>
                        <a href="criar_doacao.php" class="flex items-center justify-center w-full bg-[#10B981] hover:bg-[#059669] text-white font-medium py-2.5 rounded-lg transition-colors mb-4 shadow-sm">+ Doar Ingrediente</a>
                    <?php elseif($tipo_usuario == 'cozinheiro'): ?>
                        <a href="criar_refeicao.php" class="flex items-center justify-center w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 rounded-lg transition-colors mb-4 shadow-sm">+ Criar Refeição</a>
                    <?php endif; ?>
                    
                    <a href="perfil.php" class="block text-center text-sm text-gray-500 hover:text-green-600 mt-2">Editar Perfil</a>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-10">
                
                <?php if($tipo_usuario != 'produtor' || !empty($doacoes)): ?>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        Ingredientes & Doações
                    </h2>
                    
                    <?php if(empty($doacoes)): ?>
                        <p class="text-gray-500 text-sm bg-white p-4 rounded-xl border border-dashed text-center">Nenhuma doação ativa.</p>
                    <?php endif; ?>

                    <div class="space-y-4">
                        <?php foreach($doacoes as $d): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow">
                                <div class="w-full md:w-48 h-48 md:h-auto bg-green-50 flex items-center justify-center flex-shrink-0 text-green-200">
                                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                </div>

                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($d['titulo']) ?></h3>
                                            <span class="text-sm text-gray-500">Produtor: <?= htmlspecialchars($d['produtor_nome']) ?></span>
                                        </div>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600"><?= strtoupper($d['status']) ?></span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($d['descricao']) ?></p>
                                    
                                    <div class="text-xs text-gray-500 mb-4 space-y-1">
                                        <p><strong>Data de colheita:</strong> <?= date('d/m/Y', strtotime($d['data_colheita'])) ?></p>
                                        <p><strong>Data limite:</strong> <?= date('d/m/Y', strtotime($d['data_limite'])) ?> <?php if($d['status'] == 'disponivel'): ?><span class="text-red-600 font-semibold"></span><?php endif; ?></p>
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-t border-gray-100">
                                        <?php if($tipo_usuario == 'distribuidor' && $d['status'] == 'disponivel'): ?>
                                            <form action="processar_acao.php" method="POST">
                                                <input type="hidden" name="acao" value="coletar">
                                                <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                <button class="text-blue-600 font-medium text-sm hover:underline">Coletar Ingrediente</button>
                                            </form>
                                        <?php elseif($d['status'] == 'coletada' && $d['distribuidor_id'] == $user_id && $tipo_usuario == 'distribuidor'): ?>
                                            <button onclick="toggleModal('modal-d-<?= $d['id'] ?>')" class="text-orange-600 font-medium text-sm hover:underline">Entregar p/ Cozinheiro</button>
                                            <div id="modal-d-<?= $d['id'] ?>" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
                                                <div class="bg-white rounded-lg p-6 w-full max-w-sm">
                                                    <h3 class="font-bold mb-4">Escolher Cozinheiro</h3>
                                                    <form action="processar_acao.php" method="POST">
                                                        <input type="hidden" name="acao" value="entregar">
                                                        <input type="hidden" name="doacao_id" value="<?= $d['id'] ?>">
                                                        <select name="cozinheiro_id" class="w-full border p-2 rounded mb-4 text-sm">
                                                            <?php foreach($cozinheiros as $coz): ?>
                                                                <option value="<?= $coz['id'] ?>"><?= htmlspecialchars($coz['nome']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button class="bg-orange-600 text-white w-full py-2 rounded">Confirmar</button>
                                                        <button type="button" onclick="toggleModal('modal-d-<?= $d['id'] ?>')" class="block text-center w-full mt-2 text-sm text-gray-500">Cancelar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php elseif($d['status'] == 'aguardando_aceite' && $d['cozinheiro_id'] == $user_id && $tipo_usuario == 'cozinheiro'): ?>
                                            <div class="flex gap-4">
                                                <form action="processar_acao.php" method="POST"><input type="hidden" name="acao" value="aceitar_entrega"><input type="hidden" name="doacao_id" value="<?= $d['id'] ?>"><button class="text-green-600 font-bold text-sm">Aceitar</button></form>
                                                <form action="processar_acao.php" method="POST"><input type="hidden" name="acao" value="recusar_entrega"><input type="hidden" name="doacao_id" value="<?= $d['id'] ?>"><button class="text-red-600 font-bold text-sm">Recusar</button></form>
                                            </div>
                                        <?php elseif($d['status'] == 'entregue'): ?>
                                            <a href="avaliar.php?tipo=doacao&id=<?= $d['id'] ?>" class="text-yellow-600 font-medium text-sm hover:underline">★ Avaliar Participantes</a>
                                            <?php if($d['ja_avaliou'] > 0): ?><span class="text-xs text-green-600 ml-2">✓ Avaliado</span><?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($tipo_usuario != 'produtor'): ?>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Refeições Prontas para Distribuição
                    </h2>

                    <?php if(empty($refeicoes)): ?>
                        <p class="text-gray-500 text-sm bg-white p-4 rounded-xl border border-dashed text-center">Nenhuma refeição disponível no momento.</p>
                    <?php endif; ?>

                    <div class="space-y-4">
                        <?php foreach($refeicoes as $m): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow">
                                <div class="w-full md:w-48 h-48 md:h-auto bg-orange-50 flex items-center justify-center flex-shrink-0 text-orange-200">
                                    <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c4.97 0 9-4.03 9-9h-1.6c-.76 2.37-2.63 4.24-5 5V22h-4.8v-4c-2.37-.76-4.24-2.63-5-5H3c0 4.97 4.03 9 9 9zM19 9c0-3.87-3.13-7-7-7S5 5.13 5 9c0 .17.02.34.05.5H12v2H5.5c.32 1.46 1.35 2.65 2.7 3.29.53.25 1.13.38 1.8.38 2.39 0 4.36-1.84 4.9-4.17H22v-2h-3.05c.03-.16.05-.33.05-.5z"/></svg>
                                </div>

                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($m['titulo']) ?></h3>
                                            <span class="text-sm text-gray-500">Cozinheiro: <?= htmlspecialchars($m['cozinheiro_nome']) ?></span>
                                        </div>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600"><?= strtoupper($m['status']) ?></span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4">
                                        Quantidade: <?= htmlspecialchars($m['quantidade']) ?>
                                    </p>
                                    <div class="text-xs text-gray-500 mb-4">
                                        <p><strong>Data de validade:</strong> <?= date('d/m/Y', strtotime($m['data_validade'])) ?></p>
                                    </div>

                                    <div class="mt-auto pt-3 border-t border-gray-100">
                                        <?php if($tipo_usuario == 'distribuidor' && $m['status'] == 'disponivel'): ?>
                                            <form action="processar_acao.php" method="POST">
                                                <input type="hidden" name="acao" value="coletar_refeicao">
                                                <input type="hidden" name="refeicao_id" value="<?= $m['id'] ?>">
                                                <button class="text-blue-600 font-medium text-sm hover:underline">Coletar para Distribuição</button>
                                            </form>
                                        <?php elseif($tipo_usuario == 'distribuidor' && $m['status'] == 'coletada' && $m['distribuidor_id'] == $user_id): ?>
                                            <form action="processar_acao.php" method="POST">
                                                <input type="hidden" name="acao" value="finalizar_distribuicao">
                                                <input type="hidden" name="refeicao_id" value="<?= $m['id'] ?>">
                                                <button class="text-green-600 font-medium text-sm hover:underline">Marcar como Entregue (Finalizar)</button>
                                            </form>
                                        <?php elseif($m['status'] == 'entregue'): ?>
                                            <a href="avaliar.php?tipo=refeicao&id=<?= $m['id'] ?>" class="text-yellow-600 font-medium text-sm hover:underline">★ Avaliar Parceiro</a>
                                            <?php if($m['ja_avaliou'] > 0): ?><span class="text-xs text-green-600 ml-2">✓ Avaliado</span><?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>