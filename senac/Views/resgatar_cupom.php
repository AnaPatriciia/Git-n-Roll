<?php
require_once '../Database/Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custo'])) {
    $custo = (int) $_POST['custo'];
    $id_usuario = $_SESSION['usuarios']['id_usuario'] ?? null;

    if (!$id_usuario) {
        echo "❌ Usuário não está logado ou id_usuario não definido na sessão.";
        exit;
    }

    $db = new Database();

    // Busca soma das recompensas
    $stmt = $db->execute("SELECT SUM(recompensa) AS total FROM checkin_diario WHERE id_usuario = :id", [':id' => $id_usuario]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $resultado['total'] ?? 0;

    if ($total >= $custo) {
        // Subtrai moedas proporcionalmente
        $consulta = $db->execute(
            "SELECT id_checkin, recompensa FROM checkin_diario WHERE id_usuario = :id AND recompensa > 0 ORDER BY data_checkin ASC",
            [':id' => $id_usuario]
        );

        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $restante = $custo;

        foreach ($registros as $linha) {
            if ($restante <= 0) break;

            $id_checkin = $linha['id_checkin'];
            $recompensa = $linha['recompensa'];
            $subtrair = min($recompensa, $restante);

            $db->execute(
                "UPDATE checkin_diario SET recompensa = recompensa - :subtrair WHERE id_checkin = :id_checkin",
                [':subtrair' => $subtrair, ':id_checkin' => $id_checkin]
            );

            $restante -= $subtrair;
        }

        echo "✅ Cupom resgatado com sucesso!";
        
    } else {
        echo "❌ Moedas insuficientes.";
    }
} else {
    echo "❌ Requisição inválida ou custo não enviado.";
}
?>
