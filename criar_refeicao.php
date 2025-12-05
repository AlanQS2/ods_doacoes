<?php
require 'db.php';
verificarLogin();

// Apenas cozinheiros
if ($_SESSION['user_tipo'] != 'cozinheiro') {
    header("Location: feed.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar ingredientes disponíveis (Doações entregues a este cozinheiro)
$stmt = $pdo->prepare("SELECT id, titulo, quantidade, unidade FROM donations WHERE cozinheiro_id = ? AND status = 'entregue'");
$stmt->execute([$user_id]);
$ingredientes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $quantidade = $_POST['quantidade'];
    $data_producao = $_POST['data_producao'];
    $data_validade = $_POST['data_validade'];
    $ingredientes_selecionados = $_POST['ingredientes'] ?? [];

    // Inserir Refeição
    $sql = "INSERT INTO meals (cozinheiro_id, titulo, quantidade, data_producao, data_validade) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $titulo, $quantidade, $data_producao, $data_validade])) {
        $meal_id = $pdo->lastInsertId();

        // Vincular Ingredientes
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
    <title>Nova Refeição - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Nova Refeição</h2>
            <a href="feed.php" class="text-gray-500 hover:text-gray-700">Cancelar</a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Prato</label>
                <input type="text" name="titulo" placeholder="Ex: Sopa de Legumes" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantidade (kg/un)</label>
                    <input type="text" name="quantidade" placeholder="Ex: 50 marmitas" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Produção</label>
                    <input type="date" name="data_producao" value="<?= date('Y-m-d') ?>" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Validade</label>
                    <input type="date" name="data_validade" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ingredientes Utilizados (Doações Recebidas)</label>
                <?php if(empty($ingredientes)): ?>
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-2 rounded">Você ainda não recebeu ingredientes registrados.</p>
                <?php else: ?>
                    <div class="max-h-40 overflow-y-auto border rounded-md p-2 space-y-2 bg-gray-50">
                        <?php foreach($ingredientes as $ing): ?>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="ingredientes[]" value="<?= $ing['id'] ?>" class="rounded text-green-600 focus:ring-green-500">
                                <span><?= htmlspecialchars($ing['titulo']) ?> (<?= $ing['quantidade'] ?> <?= $ing['unidade'] ?>)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white py-3 rounded-md hover:bg-orange-700 transition font-medium mt-4">
                Publicar Refeição
            </button>
        </form>
    </div>
</body>
</html>