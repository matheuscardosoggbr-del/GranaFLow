<?php

namespace App\Models;

use App\Core\Model;

class Poupanca extends Model
{
    public function getTotalGuardado($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(valor), 0) AS total FROM dinheiro_guardado WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'];
    }

    public function getHistorico($id_usuario, $limite = 5)
    {
        $stmt = $this->db->prepare("SELECT * FROM dinheiro_guardado WHERE id_usuario = ? ORDER BY data_registro DESC LIMIT ?");
        $stmt->bind_param("ii", $id_usuario, $limite);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function guardar($id_usuario, $valor, $descricao = 'Dinheiro guardado')
    {
        $stmt = $this->db->prepare("INSERT INTO dinheiro_guardado (id_usuario, valor, descricao) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $id_usuario, $valor, $descricao);
        return $stmt->execute();
    }
}

