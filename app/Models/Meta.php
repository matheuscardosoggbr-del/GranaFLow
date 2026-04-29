<?php

namespace App\Models;

use App\Core\Model;

class Meta extends Model
{
    /**
     * Obtém todas as metas do usuário
     */
    public function getMetas($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT * FROM metas WHERE id_usuario = ? ORDER BY data_criacao DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtém uma meta específica
     */
    public function getMetaById($id, $id_usuario)
    {
        $stmt = $this->db->prepare("SELECT * FROM metas WHERE id_meta = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id, $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Adiciona nova meta
     */
    public function adicionar($id_usuario, $nome, $valor, $tipo = 'gasto')
    {
        $stmt = $this->db->prepare("INSERT INTO metas (id_usuario, nome_meta, valor_limite, tipo, valor_guardado) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("isds", $id_usuario, $nome, $valor, $tipo);
        return $stmt->execute();
    }

    /**
     * Atualiza uma meta
     */
    public function atualizar($id_meta, $nome, $valor, $tipo = 'gasto')
    {
        $stmt = $this->db->prepare("UPDATE metas SET nome_meta = ?, valor_limite = ?, tipo = ? WHERE id_meta = ?");
        $stmt->bind_param("sdsi", $nome, $valor, $tipo, $id_meta);
        return $stmt->execute();
    }

    /**
     * Guarda dinheiro em uma meta
     */
    public function guardarDinheiro($id_meta, $id_usuario, $valor)
    {
        $stmt = $this->db->prepare("UPDATE metas SET valor_guardado = valor_guardado + ? WHERE id_meta = ? AND id_usuario = ?");
        $stmt->bind_param("dii", $valor, $id_meta, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Remove dinheiro de uma meta
     */
    public function removerDinheiro($id_meta, $id_usuario, $valor)
    {
        $stmt = $this->db->prepare("UPDATE metas SET valor_guardado = GREATEST(0, valor_guardado - ?) WHERE id_meta = ? AND id_usuario = ?");
        $stmt->bind_param("dii", $valor, $id_meta, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Deleta uma meta
     */
    public function deletar($id_meta, $id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM metas WHERE id_meta = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id_meta, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Obtém progresso das metas
     */
    public function getProgresso($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT 
            COUNT(*) as total_metas,
            SUM(CASE WHEN tipo = 'gasto' THEN 1 ELSE 0 END) as metas_gasto,
            SUM(CASE WHEN tipo = 'reserva' THEN 1 ELSE 0 END) as metas_reserva,
            SUM(valor_guardado) as total_guardado,
            SUM(valor_limite) as total_limite
            FROM metas WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

