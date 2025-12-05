<?php
require 'db.php';
verificarLogin();

// Segurança: Apenas administrador
if ($_SESSION['user_tipo'] != 'administrador') {
    header("Location: feed.php");
    exit;
}

// Estatísticas
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE tipo != 'administrador'")->fetchColumn(),
    'donations' => $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn(),
    'meals' => $pdo->query("SELECT COUNT(*) FROM meals")->fetchColumn(),
];

// Buscas (Limitadas para performance)
$users = $pdo->query("SELECT id, nome, email, tipo, cidade, banned FROM users WHERE tipo != 'administrador' ORDER BY created_at DESC LIMIT 50")->fetchAll();
$donations = $pdo->query("SELECT d.id, d.titulo, d.status, u.nome as produtor FROM donations d JOIN users u ON d.produtor_id = u.id ORDER BY d.created_at DESC LIMIT 50")->fetchAll();
$meals = $pdo->query("SELECT m.id, m.titulo, m.status, u.nome as cozinheiro FROM meals m JOIN users u ON m.cozinheiro_id = u.id ORDER BY m.created_at DESC LIMIT 50")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Painel Admin - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script>
        function confirmAction(message) {
            return confirm(message);
        }
        function switchTab(tabId) {
            // Esconde todos
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Reseta botões
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-green-600', 'text-green-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });
            // Mostra o atual
            document.getElementById(tabId).classList.remove('hidden');
            // Ativa botão atual
            const btn = document.getElementById('btn-' + tabId);
            btn.classList.add('border-green-600', 'text-green-600');
            btn.classList.remove('border-transparent', 'text-gray-500');
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen pb-10">
    
    <header class="bg-gray-900 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-green-500 p-1.5 rounded text-gray-900">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </div>
                <span class="font-bold text-lg tracking-tight">Admin</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400 hidden sm:block uppercase tracking-wider font-semibold">Sistema de Gestão</span>
                <a href="logout.php" class="text-red-400 hover:text-red-300 text-sm font-medium border border-red-900 bg-red-900/20 px-3 py-1.5 rounded transition-colors">Sair</a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6 md:py-8 max-w-6xl">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Usuários</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['users'] ?></p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Doações</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['donations'] ?></p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg text-green-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Refeições</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $stats['meals'] ?></p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg text-orange-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
        </div>

        <div class="border-b border-gray-200 mb-6 overflow-x-auto no-scrollbar">
            <nav class="flex gap-8 min-w-max px-2">
                <button id="btn-tab-users" onclick="switchTab('tab-users')" class="tab-btn pb-3 border-b-2 font-bold text-sm transition-colors border-green-600 text-green-600 focus:outline-none">
                    Gerenciar Usuários
                </button>
                <button id="btn-tab-donations" onclick="switchTab('tab-donations')" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors focus:outline-none">
                    Moderar Doações
                </button>
                <button id="btn-tab-meals" onclick="switchTab('tab-meals')" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors focus:outline-none">
                    Moderar Refeições
                </button>
            </nav>
        </div>

        <div id="tab-users" class="tab-content">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 md:px-6">Usuário</th>
                            <th class="px-4 py-3 md:px-6 hidden md:table-cell">Tipo / Local</th>
                            <th class="px-4 py-3 md:px-6 text-center">Status</th>
                            <th class="px-4 py-3 md:px-6 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php foreach($users as $u): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 md:px-6">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($u['nome']) ?></div>
                                <div class="text-gray-500 text-xs truncate max-w-[150px]"><?= htmlspecialchars($u['email']) ?></div>
                                <div class="md:hidden mt-1 text-[10px] uppercase text-gray-400 font-bold"><?= $u['tipo'] ?></div>
                            </td>
                            
                            <td class="px-4 py-3 md:px-6 hidden md:table-cell">
                                <span class="bg-gray-100 text-gray-700 py-1 px-2 rounded text-xs font-bold uppercase mr-2"><?= $u['tipo'] ?></span>
                                <span class="text-gray-500 text-xs"><?= htmlspecialchars($u['cidade']) ?></span>
                            </td>

                            <td class="px-4 py-3 md:px-6 text-center">
                                <?php if($u['banned']): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">BANIDO</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">ATIVO</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3 md:px-6 text-right">
                                <form action="admin_acoes.php" method="POST" onsubmit="return confirmAction('Tem certeza?');">
                                    <input type="hidden" name="acao" value="banir_usuario">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <?php if($u['banned']): ?>
                                        <button class="text-green-600 bg-green-50 hover:bg-green-100 border border-green-200 font-medium text-xs px-3 py-1.5 rounded-lg transition-colors">Reativar</button>
                                    <?php else: ?>
                                        <button class="text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 font-medium text-xs px-3 py-1.5 rounded-lg transition-colors">Banir</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-donations" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 md:px-6">Doação</th>
                            <th class="px-4 py-3 md:px-6 hidden md:table-cell">Produtor</th>
                            <th class="px-4 py-3 md:px-6 text-center">Status</th>
                            <th class="px-4 py-3 md:px-6 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php foreach($donations as $d): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 md:px-6">
                                <div class="font-bold text-gray-900 line-clamp-1"><?= htmlspecialchars($d['titulo']) ?></div>
                                <div class="md:hidden text-xs text-gray-500 mt-0.5">Por: <?= htmlspecialchars($d['produtor']) ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 hidden md:table-cell text-gray-600">
                                <?= htmlspecialchars($d['produtor']) ?>
                            </td>
                            <td class="px-4 py-3 md:px-6 text-center">
                                <span class="text-[10px] uppercase font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-full whitespace-nowrap"><?= $d['status'] ?></span>
                            </td>
                            <td class="px-4 py-3 md:px-6 text-right">
                                <form action="admin_acoes.php" method="POST" onsubmit="return confirmAction('Apagar item permanentemente?');">
                                    <input type="hidden" name="acao" value="excluir_doacao">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button class="text-red-600 bg-white hover:bg-red-50 border border-red-200 font-bold text-xs px-3 py-1.5 rounded-lg transition-colors">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-meals" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 md:px-6">Refeição</th>
                            <th class="px-4 py-3 md:px-6 hidden md:table-cell">Cozinheiro</th>
                            <th class="px-4 py-3 md:px-6 text-center">Status</th>
                            <th class="px-4 py-3 md:px-6 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php foreach($meals as $m): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 md:px-6">
                                <div class="font-bold text-gray-900 line-clamp-1"><?= htmlspecialchars($m['titulo']) ?></div>
                                <div class="md:hidden text-xs text-gray-500 mt-0.5">Chef: <?= htmlspecialchars($m['cozinheiro']) ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 hidden md:table-cell text-gray-600">
                                <?= htmlspecialchars($m['cozinheiro']) ?>
                            </td>
                            <td class="px-4 py-3 md:px-6 text-center">
                                <span class="text-[10px] uppercase font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-full whitespace-nowrap"><?= $m['status'] ?></span>
                            </td>
                            <td class="px-4 py-3 md:px-6 text-right">
                                <form action="admin_acoes.php" method="POST" onsubmit="return confirmAction('Apagar refeição permanentemente?');">
                                    <input type="hidden" name="acao" value="excluir_refeicao">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button class="text-red-600 bg-white hover:bg-red-50 border border-red-200 font-bold text-xs px-3 py-1.5 rounded-lg transition-colors">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>