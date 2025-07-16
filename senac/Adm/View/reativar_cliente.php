<?php
require_once '../../Database/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe o id_usuario via POST
    $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : null;

    
    if ($id_usuario) {
        
        $db = new Database('usuarios'); 

        // A consulta para atualizar o status de 'ativo' para 1 (reativar)
        $sql = "UPDATE usuarios SET ativo = 1 WHERE id_usuario = ?";

       
        $result = $db->execute($sql, [$id_usuario]);

        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status do cliente.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de usuário não fornecido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
?>
