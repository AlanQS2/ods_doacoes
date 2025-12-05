<?php
require 'db.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];
    $user_id = $_SESSION['user_id'];
    $tipo_usuario = $_SESSION['user_tipo'];

    try {
        // --- FLUXO DE DOAÇÃO (Mantido) ---
        if (isset($_POST['doacao_id'])) {
            $doacao_id = $_POST['doacao_id'];
            
            if ($acao == 'coletar' && $tipo_usuario == 'distribuidor') {
                $stmt = $pdo->prepare("UPDATE donations SET status = 'coletada', distribuidor_id = ? WHERE id = ? AND status = 'disponivel'");
                $stmt->execute([$user_id, $doacao_id]);
            } 
            elseif ($acao == 'entregar' && $tipo_usuario == 'distribuidor') {
                $cozinheiro_id = $_POST['cozinheiro_id'];
                $stmt = $pdo->prepare("UPDATE donations SET status = 'aguardando_aceite', cozinheiro_id = ? WHERE id = ? AND status = 'coletada' AND distribuidor_id = ?");
                $stmt->execute([$cozinheiro_id, $doacao_id, $user_id]);
            }
            elseif ($acao == 'aceitar_entrega' && $tipo_usuario == 'cozinheiro') {
                $stmt = $pdo->prepare("UPDATE donations SET status = 'entregue' WHERE id = ? AND status = 'aguardando_aceite' AND cozinheiro_id = ?");
                $stmt->execute([$doacao_id, $user_id]);
            }
            elseif ($acao == 'recusar_entrega' && $tipo_usuario == 'cozinheiro') {
                $stmt = $pdo->prepare("UPDATE donations SET status = 'coletada', cozinheiro_id = NULL WHERE id = ? AND status = 'aguardando_aceite' AND cozinheiro_id = ?");
                $stmt->execute([$doacao_id, $user_id]);
            }
        }

        // --- NOVO: FLUXO DE REFEIÇÃO ---
        if (isset($_POST['refeicao_id'])) {
            $refeicao_id = $_POST['refeicao_id'];

            if ($acao == 'coletar_refeicao' && $tipo_usuario == 'distribuidor') {
                // Distribuidor coleta a refeição do cozinheiro
                $stmt = $pdo->prepare("UPDATE meals SET status = 'coletada', distribuidor_id = ? WHERE id = ? AND status = 'disponivel'");
                $stmt->execute([$user_id, $refeicao_id]);
            }
            elseif ($acao == 'finalizar_distribuicao' && $tipo_usuario == 'distribuidor') {
                // Distribuidor finaliza (entrega para beneficiários)
                $stmt = $pdo->prepare("UPDATE meals SET status = 'entregue' WHERE id = ? AND status = 'coletada' AND distribuidor_id = ?");
                $stmt->execute([$refeicao_id, $user_id]);
            }
        }

        header("Location: feed.php");
        exit;

    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
} else {
    header("Location: feed.php");
    exit;
}
?>