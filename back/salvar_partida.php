<?php
session_start();
require_once '../startup/connectBD.php'; // Arquivo com a conexão ao banco de dados

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id']; // ID do jogador logado

$dadosPartida = json_decode(file_get_contents("php://input"), true); // Captura o JSON enviado

$nivel = $dadosPartida['nivel'];
$modo = $dadosPartida['modo'];
$tempo_total = $dadosPartida['tempo_total'];
$erros_historico = $dadosPartida['erros'];
$pontuacao_jogador1 = $dadosPartida['pontos_jogador1'];
$pontuacao_jogador2 = isset($dadosPartida['pontos_jogador2']) ? $dadosPartida['pontos_jogador2'] : null; // Pontuação do jogador 2 no modo dupla
$jogador2_id = isset($dadosPartida['jogador2_id']) ? $dadosPartida['jogador2_id'] : null; // ID do jogador 2, se houver
$vencedor_id = $pontuacao_jogador1 > $pontuacao_jogador2 ? $user_id : $jogador2_id; // Define vencedor no modo dupla

$conn->begin_transaction();

try {
    $stmtPartida = $conn->prepare("INSERT INTO `partidas` (`data_partida`, `nivel_dificuldade`, `modo_jogo`, `tempo_total`, `vencedor_id`) VALUES (NOW(), ?, ?, ?, ?)");
    $stmtPartida->bind_param("sssi", $nivel, $modo, $tempo_total, $vencedor_id);
    $stmtPartida->execute();

    $partida_id = $conn->insert_id;

    if ($modo === 'dupla') {
        $stmtDupla = $conn->prepare("INSERT INTO `partidas_dupla` (`partida_id`, `jogador1_id`, `jogador2_id`, `pontuacao_jogador1`, `pontuacao_jogador2`) VALUES (?, ?, ?, ?, ?)");
        $stmtDupla->bind_param("iiiii", $partida_id, $user_id, $jogador2_id, $pontuacao_jogador1, $pontuacao_jogador2);
        $stmtDupla->execute();

        $stmtHistorico1 = $conn->prepare("INSERT INTO `historico` (`partida_id`, `jogador_id`, `erros_historico`, `pontuacao_historico`) VALUES (?, ?, ?, ?)");
        $stmtHistorico1->bind_param("iiii", $partida_id, $user_id, $erros_historico, $pontuacao_jogador1);
        $stmtHistorico1->execute();

        $stmtHistorico2 = $conn->prepare("INSERT INTO `historico` (`partida_id`, `jogador_id`, `erros_historico`, `pontuacao_historico`) VALUES (?, ?, ?, ?)");
        $stmtHistorico2->bind_param("iiii", $partida_id, $jogador2_id, $erros_historico, $pontuacao_jogador2);
        $stmtHistorico2->execute();

    } else {
        $stmtSolo = $conn->prepare("INSERT INTO `partidas_solo` (`partida_id`, `erros_partidas_solo`) VALUES (?, ?)");
        $stmtSolo->bind_param("ii", $partida_id, $erros_historico);
        $stmtSolo->execute();

        $stmtHistorico = $conn->prepare("INSERT INTO `historico` (`partida_id`, `jogador_id`, `erros_historico`, `pontuacao_historico`) VALUES (?, ?, ?, ?)");
        $stmtHistorico->bind_param("iiii", $partida_id, $user_id, $erros_historico, $pontuacao_jogador1);
        $stmtHistorico->execute();
    }

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Partida salva com sucesso!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar partida: ' . $e->getMessage()]);
}
?>
