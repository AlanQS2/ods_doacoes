<?php
require 'db.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];
    $doacao_id = $_POST['doacao_id'];
    $user_id = $_SESSION['user_id'];
    $tipo_usuario = $_SESSION['user_tipo'];

    try {
        // --- AÇÕES DO DISTRIBUIDOR ---
        
        if ($acao == 'coletar' && $tipo_usuario == 'distribuidor') {
            // Coleta a doação disponível
            $stmt = $pdo->prepare("UPDATE donations SET status = 'coletada', distribuidor_id = ? WHERE id = ? AND status = 'disponivel'");
            $stmt->execute([$user_id, $doacao_id]);
        } 
        
        elseif ($acao == 'entregar' && $tipo_usuario == 'distribuidor') {
            // Distribuidor seleciona o cozinheiro, mas o status vira 'aguardando_aceite'
            $cozinheiro_id = $_POST['cozinheiro_id'];
            $stmt = $pdo->prepare("UPDATE donations SET status = 'aguardando_aceite', cozinheiro_id = ? WHERE id = ? AND status = 'coletada' AND distribuidor_id = ?");
            $stmt->execute([$cozinheiro_id, $doacao_id, $user_id]);
        }

        // --- AÇÕES DO COZINHEIRO ---

        elseif ($acao == 'aceitar_entrega' && $tipo_usuario == 'cozinheiro') {
            // Cozinheiro aceita: status vira 'entregue'
            $stmt = $pdo->prepare("UPDATE donations SET status = 'entregue' WHERE id = ? AND status = 'aguardando_aceite' AND cozinheiro_id = ?");
            $stmt->execute([$doacao_id, $user_id]);
        }

        elseif ($acao == 'recusar_entrega' && $tipo_usuario == 'cozinheiro') {
            // Cozinheiro recusa: status volta para 'coletada' e limpa o cozinheiro_id
            // Assim, volta para o feed do distribuidor escolher outro
            $stmt = $pdo->prepare("UPDATE donations SET status = 'coletada', cozinheiro_id = NULL WHERE id = ? AND status = 'aguardando_aceite' AND cozinheiro_id = ?");
            $stmt->execute([$doacao_id, $user_id]);
        }

        header("Location: feed.php");
        exit;

    } catch (Exception $e) {
        die("Erro ao processar ação: " . $e->getMessage());
    }
} else {
    header("Location: feed.php");
    exit;
}
?>