<?php

namespace App\Models;

use App\Core\Model;

class Receita extends Model
{
    public function getReceitas($usuario_id, $filtro_mes = null, $ordem = 'data_desc', $busca = null)
    {
        $sql = "SELECT r.*, m.simbolo
                FROM receitas r
                JOIN moedas m ON r.id_moeda = m.id_moeda
                WHERE r.id_usuario = ?";

        $params = [$usuario_id];
        $tipos = "i";

        if ($filtro_mes) {
            $sql .= " AND DATE_FORMAT(r.data_receita, '%Y-%m') = ?";
            $params[] = $filtro_mes;
            $tipos .= "s";
        }

        if (!empty($busca)) {
            $sql .= " AND r.descricao LIKE ?";
            $params[] = '%' . $busca . '%';
            $tipos .= "s";
        }

        switch ($ordem) {
            case 'valor_asc':
                $sql .= " ORDER BY r.valor ASC";
                break;
            case 'valor_desc':
                $sql .= " ORDER BY r.valor DESC";
                break;
            case 'data_asc':
                $sql .= " ORDER BY r.data_receita ASC";
                break;
            default:
                $sql .= " ORDER BY r.data_receita DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getReceitaById($id, $usuario_id)
    {
        $sql = "SELECT r.*, m.simbolo
                FROM receitas r
                JOIN moedas m ON r.id_moeda = m.id_moeda
                WHERE r.id_receita = ? AND r.id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function adicionar($id_usuario, $descricao, $valor, $data_receita, $id_moeda = 1)
    {
        $sql = "INSERT INTO receitas (id_usuario, id_moeda, descricao, valor, data_receita)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisds", $id_usuario, $id_moeda, $descricao, $valor, $data_receita);
        return $stmt->execute();
    }

    public function atualizar($id_receita, $descricao, $valor, $data_receita)
    {
        $sql = "UPDATE receitas SET descricao = ?, valor = ?, data_receita = ? WHERE id_receita = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdsi", $descricao, $valor, $data_receita, $id_receita);
        return $stmt->execute();
    }

    public function deletar($id_receita, $id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM receitas WHERE id_receita = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id_receita, $id_usuario);
        return $stmt->execute();
    }
}
