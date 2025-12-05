<?php
require 'db.php';
verificarLogin();

// Apenas produtores podem acessar
if ($_SESSION['user_tipo'] != 'produtor') {
    header("Location: feed.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <title>Nova Doação - AlimentoSolidário</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Nova Doação</h2>
            <a href="feed.php" class="text-gray-500 hover:text-gray-700">Cancelar</a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">O que você está doando?</label>
                <input type="text" name="titulo" placeholder="Ex: Caixas de Tomates" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="descricao" rows="3" placeholder="Detalhes sobre a qualidade, maturação..." required class="mt-1 w-full border border-gray-300 rounded-md p-2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="tipo" class="mt-1 w-full border border-gray-300 rounded-md p-2">
                        <option value="frutas">Frutas</option>
                        <option value="vegetais">Vegetais</option>
                        <option value="ambos">Ambos</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <div class="w-2/3">
                        <label class="block text-sm font-medium text-gray-700">Qtd</label>
                        <input type="number" name="quantidade" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-sm font-medium text-gray-700">Un.</label>
                        <select name="unidade" class="mt-1 w-full border border-gray-300 rounded-md p-2">
                            <option value="kg">kg</option>
                            <option value="caixas">cx</option>
                            <option value="unidades">un</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Data Colheita</label>
                    <input type="date" name="data_colheita" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Validade Estimada</label>
                    <input type="date" name="data_limite" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 transition font-medium mt-4">
                Publicar Doação
            </button>
        </form>
    </div>
</body>
</html>