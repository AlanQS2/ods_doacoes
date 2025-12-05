<?php
require 'db.php';
verificarLogin();

if (!isset($_GET['doacao']) || !isset($_GET['alvo'])) {
    header("Location: feed.php");
    exit;
}

$doacao_id = $_GET['doacao'];
$avaliado_id = $_GET['alvo'];
$avaliador_id = $_SESSION['user_id'];

// Processar Envio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica duplicidade no backend também por segurança
    $check = $pdo->prepare("SELECT id FROM reviews WHERE doacao_id = ? AND avaliador_id = ?");
    $check->execute([$doacao_id, $avaliador_id]);
    
    if ($check->rowCount() == 0) {
        $nota = $_POST['nota'];
        $comentario = $_POST['comentario'];
        $stmt = $pdo->prepare("INSERT INTO reviews (doacao_id, avaliador_id, avaliado_id, nota, comentario) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$doacao_id, $avaliador_id, $avaliado_id, $nota, $comentario]);
    }
    
    header("Location: feed.php");
    exit;
}

// Buscar dados para exibir na tela
$stmtUser = $pdo->prepare("SELECT nome, tipo FROM users WHERE id = ?");
$stmtUser->execute([$avaliado_id]);
$alvo = $stmtUser->fetch();

$stmtDoacao = $pdo->prepare("SELECT titulo FROM donations WHERE id = ?");
$stmtDoacao->execute([$doacao_id]);
$doacao = $stmtDoacao->fetch();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Avaliar - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-2 text-gray-800">Avaliar Parceiro</h2>
        <div class="mb-6 text-sm text-gray-600">
            <p>Doação: <strong><?= htmlspecialchars($doacao['titulo']) ?></strong></p>
            <p>Avaliado: <strong><?= htmlspecialchars($alvo['nome']) ?></strong> (<?= ucfirst($alvo['tipo']) ?>)</p>
        </div>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sua Nota</label>
                <div class="flex justify-center gap-4 text-4xl">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="nota" value="<?= $i ?>" required class="peer sr-only">
                            <span class="text-gray-300 peer-checked:text-yellow-400 group-hover:text-yellow-300 transition">★</span>
                            <span class="text-xs text-gray-400 absolute -bottom-4 left-1/2 -translate-x-1/2"><?= $i ?></span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comentário</label>
                <textarea name="comentario" rows="3" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" placeholder="Como foi a experiência?"></textarea>
            </div>

            <div class="flex gap-3">
                <a href="feed.php" class="flex-1 text-center py-2 text-gray-600 border rounded hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700">Enviar</button>
            </div>
        </form>
    </div>
</body>
</html>