<?php
require 'db.php';
verificarLogin();

// Segurança: Apenas administrador pode acessar
if ($_SESSION['user_tipo'] != 'administrador') {
    header("Location: feed.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];

    try {
        if ($acao == 'banir_usuario') {
            $id_alvo = $_POST['id'];
            // Toggle (Se for 0 vira 1, se for 1 vira 0)
            $stmt = $pdo->prepare("UPDATE users SET banned = NOT banned WHERE id = ? AND tipo != 'administrador'");
            $stmt->execute([$id_alvo]);
        }
        
        elseif ($acao == 'excluir_doacao') {
            $id_alvo = $_POST['id'];
            // Excluir avaliações primeiro (constraints)
            $pdo->prepare("DELETE FROM reviews WHERE doacao_id = ?")->execute([$id_alvo]);
            $pdo->prepare("DELETE FROM meal_ingredients WHERE donation_id = ?")->execute([$id_alvo]);
            $pdo->prepare("DELETE FROM donations WHERE id = ?")->execute([$id_alvo]);
        }
        
        elseif ($acao == 'excluir_refeicao') {
            $id_alvo = $_POST['id'];
            // Excluir avaliações e ingredientes
            $pdo->prepare("DELETE FROM meal_reviews WHERE refeicao_id = ?")->execute([$id_alvo]);
            $pdo->prepare("DELETE FROM meal_ingredients WHERE meal_id = ?")->execute([$id_alvo]);
            $pdo->prepare("DELETE FROM meals WHERE id = ?")->execute([$id_alvo]);
        }

        header("Location: feed_admin.php");
        exit;

    } catch (Exception $e) {
        die("Erro ao processar ação administrativa: " . $e->getMessage());
    }
}
?>