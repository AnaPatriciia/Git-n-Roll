<?php

//     private $conn;
//     private $id_usuario;

//     public function __construct($conn, $id_usuario) {
//         $this->conn = $conn;
//         $this->id_usuario = $id_usuario;
//     }

//     public function jaFezCheckinHoje() {
//         $hoje = date('Y-m-d');
//         $query = "SELECT * FROM checkin_diario WHERE id_usuario = ? AND data_checkin = ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("is", $this->id_usuario, $hoje);
//         $stmt->execute();
//         return $stmt->get_result()->num_rows > 0;
//     }

//     public function diaAtual() {
//         $query = "SELECT MAX(data_checkin) as ultima_data, COUNT(*) as total_dias FROM checkin_diario WHERE id_usuario = ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $this->id_usuario);
//         $stmt->execute();
//         $result = $stmt->get_result()->fetch_assoc();

//         $ultima_data = $result['ultima_data'];
//         $total_dias = $result['total_dias'];

//         if ($ultima_data === date('Y-m-d', strtotime('-1 day'))) {
//             return $total_dias + 1;
//         } elseif ($ultima_data === date('Y-m-d')) {
//             return $total_dias;
//         } else {
//             return 1;
//         }
//     }

//     public function registrarCheckin() {
//         if ($this->jaFezCheckinHoje()) {
//             return ['status' => 'erro', 'mensagem' => 'Você já fez check-in hoje.'];
//         }

//         $dia = $this->diaAtual();
//         $hoje = date('Y-m-d');
//         $recompensa = 1;

//         $query = "INSERT INTO checkin_diario (id_usuario, data_checkin, dia_sequencia, recompensa)
//                   VALUES (?, ?, ?, ?)";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("isii", $this->id_usuario, $hoje, $dia, $recompensa);

//         if ($stmt->execute()) {
//             return ['status' => 'ok', 'mensagem' => "Check-in do dia {$dia} realizado com sucesso!"];
//         } else {
//             return ['status' => 'erro', 'mensagem' => 'Erro ao registrar check-in.'];
//         }
//     }
// }
?>

<?php
class CheckinDiario {
    private $conn;
    private $id_usuario;

    public function __construct($conn, $id_usuario) {
        $this->conn = $conn;
        $this->id_usuario = $id_usuario;
    }

    public function jaFezCheckinHoje(): bool {
        // $hoje = date('Y-m-d');
        $data_hoje = date('Y-m-d', strtotime('+1 day')); // simula amanhã
        $query = "SELECT 1 FROM checkin_diario WHERE id_usuario = ? AND data_checkin = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $this->id_usuario, $data_hoje);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }



    public function ultimoCheckin() {
    $query = "SELECT data_checkin, dia_sequencia FROM checkin_diario WHERE id_usuario = ? ORDER BY data_checkin DESC LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $this->id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}
public function registrarCheckin(): array {
    if ($this->jaFezCheckinHoje()) {
        return ['status' => 'erro', 'mensagem' => 'Você já fez check-in hoje.'];
    }

    $hoje = date('Y-m-d');
    $ontem = date('Y-m-d', strtotime('-1 day'));
    $dia = 1;

    $ultimo = $this->ultimoCheckin();

    if ($ultimo) {
        if ($ultimo['data_checkin'] === $ontem) {
            $dia = $ultimo['dia_sequencia'] + 1;
        } else {
            $dia = 1;
        }
    }

    $recompensa = 1;

    try {
        $query = "INSERT INTO checkin_diario (id_usuario, data_checkin, dia_sequencia, recompensa)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isii", $this->id_usuario, $hoje, $dia, $recompensa);
        $stmt->execute();

        return ['status' => 'ok', 'mensagem' => "Check-in do dia {$dia} registrado com sucesso!"];
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) { // Código 1062 = Duplicate entry
            return ['status' => 'erro', 'mensagem' => 'Check-in já registrado para hoje.'];
        } else {
            return ['status' => 'erro', 'mensagem' => 'Erro ao salvar check-in: ' . $e->getMessage()];
        }
    }
}


    function getTotalMoedas($conn, $id_usuario) {
    $query = "SELECT SUM(recompensa) as total_moedas FROM checkin_diario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_moedas'] ?? 0;
}



}


?>


