<?php
require 'db.php';
verificarLogin();

if ($_SESSION['user_tipo'] != 'produtor') {
    header("Location: feed.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... Lógica mantida ...
    $produtor_id = $_SESSION['user_id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];
    $quantidade = $_POST['quantidade'];
    $unidade = $_POST['unidade'];
    $data_colheita = $_POST['data_colheita'];
    $data_limite = $_POST['data_limite'];

    $sql = "INSERT INTO donations (produtor_id, titulo, descricao, tipo, quantidade, unidade, data_colheita, data_limite) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$produtor_id, $titulo, $descricao, $tipo, $quantidade, $unidade, $data_colheita, $data_limite]);

    header("Location: feed.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Doação - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Nova Doação</h2>
            <a href="feed.php" class="text-sm text-gray-500 hover:text-red-500 font-medium transition-colors">Cancelar</a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">O que você está doando?</label>
                <input type="text" name="titulo" placeholder="Ex: Caixas de Tomates" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3" placeholder="Detalhes sobre a qualidade, maturação..." required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none text-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="frutas">Frutas</option>
                        <option value="vegetais">Vegetais</option>
                        <option value="ambos">Ambos</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <div class="w-2/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qtd</label>
                        <input type="number" name="quantidade" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Un.</label>
                        <select name="unidade" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                            <option value="kg">kg</option>
                            <option value="caixas">cx</option>
                            <option value="un">un</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Colheita</label>
                    <input type="date" name="data_colheita" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none text-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Validade Estimada</label>
                    <input type="date" name="data_limite" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none text-gray-600">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition-colors mt-6 shadow-sm">
                Publicar Doação
            </button>
        </form>
    </div>
</body>
</html>