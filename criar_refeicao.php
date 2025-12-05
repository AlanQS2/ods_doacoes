<?php
require 'db.php';
verificarLogin();

if ($_SESSION['user_tipo'] != 'cozinheiro') {
    header("Location: feed.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar ingredientes disponíveis
$stmt = $pdo->prepare("SELECT id, titulo, quantidade, unidade FROM donations WHERE cozinheiro_id = ? AND status = 'entregue'");
$stmt->execute([$user_id]);
$ingredientes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... Lógica mantida ...
    $titulo = $_POST['titulo'];
    $quantidade = $_POST['quantidade'];
    $data_producao = $_POST['data_producao'];
    $data_validade = $_POST['data_validade'];
    $ingredientes_selecionados = $_POST['ingredientes'] ?? [];

    $sql = "INSERT INTO meals (cozinheiro_id, titulo, quantidade, data_producao, data_validade) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $titulo, $quantidade, $data_producao, $data_validade])) {
        $meal_id = $pdo->lastInsertId();
        if (!empty($ingredientes_selecionados)) {
            $sql_ing = "INSERT INTO meal_ingredients (meal_id, donation_id) VALUES (?, ?)";
            $stmt_ing = $pdo->prepare($sql_ing);
            foreach ($ingredientes_selecionados as $donation_id) {
                $stmt_ing->execute([$meal_id, $donation_id]);
            }
        }
        header("Location: feed.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Refeição - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Nova Refeição</h2>
            <a href="feed.php" class="text-sm text-gray-500 hover:text-red-500 font-medium transition-colors">Cancelar</a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Prato</label>
                <input type="text" name="titulo" placeholder="Ex: Sopa de Legumes" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade (kg/un)</label>
                <input type="text" name="quantidade" placeholder="Ex: 50 marmitas" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produção</label>
                    <input type="date" name="data_producao" value="<?= date('Y-m-d') ?>" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Validade</label>
                    <input type="date" name="data_validade" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none text-gray-600">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ingredientes Utilizados (Doações Recebidas)</label>
                <?php if(empty($ingredientes)): ?>
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-lg border border-dashed text-center">Você ainda não recebeu ingredientes registrados.</p>
                <?php else: ?>
                    <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2 bg-gray-50">
                        <?php foreach($ingredientes as $ing): ?>
                            <label class="flex items-center gap-3 p-2 bg-white rounded border border-gray-100 hover:bg-orange-50 transition-colors cursor-pointer">
                                <input type="checkbox" name="ingredientes[]" value="<?= $ing['id'] ?>" class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium"><?= htmlspecialchars($ing['titulo']) ?> <span class="text-gray-400 font-normal">(<?= $ing['quantidade'] ?> <?= $ing['unidade'] ?>)</span></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-lg font-bold hover:bg-orange-700 transition-colors mt-6 shadow-sm">
                Publicar Refeição
            </button>
        </form>
    </div>
</body>
</html>