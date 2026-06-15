<?php

namespace App\Models;

use App\Core\Model;

class Gasto extends Model
{
    /**
     * Obtém todos os gastos com filtros opcionais
     */
    public function getGastos($usuario_id, $filtro_categoria = null, $filtro_mes = null, $ordem = 'data_desc', $busca = null)
    {
        $sql = "SELECT g.*, c.nome AS categoria, m.simbolo
                FROM gastos g
                JOIN categorias c ON g.id_categoria = c.id_categoria
                JOIN moedas m ON g.id_moeda = m.id_moeda
                WHERE g.id_usuario = ?";

        $params = [$usuario_id];
        $tipos = "i";

        if ($filtro_categoria) {
            $sql .= " AND g.id_categoria = ?";
            $params[] = intval($filtro_categoria);
            $tipos .= "i";
        }

        if ($filtro_mes) {
            $sql .= " AND DATE_FORMAT(g.data_gasto, '%Y-%m') = ?";
            $params[] = $filtro_mes;
            $tipos .= "s";
        }

        if (!empty($busca)) {
            $sql .= " AND (g.descricao LIKE ? OR c.nome LIKE ?)";
            $termo = '%' . $busca . '%';
            $params[] = $termo;
            $params[] = $termo;
            $tipos .= "ss";
        }

        // Aplicar ordenação
        switch ($ordem) {
            case 'valor_asc':
                $sql .= " ORDER BY g.valor ASC";
                break;
            case 'valor_desc':
                $sql .= " ORDER BY g.valor DESC";
                break;
            case 'data_asc':
                $sql .= " ORDER BY g.data_gasto ASC";
                break;
            case 'categoria':
                $sql .= " ORDER BY c.nome ASC";
                break;
            default:
                $sql .= " ORDER BY g.data_gasto DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtém um gasto específico pelo ID
     */
    public function getGastoById($id, $usuario_id)
    {
        $sql = "SELECT g.*, c.nome AS categoria, m.simbolo
                FROM gastos g
                JOIN categorias c ON g.id_categoria = c.id_categoria
                JOIN moedas m ON g.id_moeda = m.id_moeda
                WHERE g.id_gasto = ? AND g.id_usuario = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Adiciona novo gasto
     */
    public function adicionar($id_usuario, $id_categoria, $descricao, $valor, $data_gasto, $id_moeda = 1)
    {
        $sql = "INSERT INTO gastos (id_usuario, id_categoria, id_moeda, descricao, valor, data_gasto)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiisds", $id_usuario, $id_categoria, $id_moeda, $descricao, $valor, $data_gasto);
        return $stmt->execute();
    }

    /**
     * Atualiza um gasto existente
     */
    public function atualizar($id_gasto, $id_categoria, $descricao, $valor, $data_gasto)
    {
        $sql = "UPDATE gastos SET id_categoria = ?, descricao = ?, valor = ?, data_gasto = ? WHERE id_gasto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isdsi", $id_categoria, $descricao, $valor, $data_gasto, $id_gasto);
        return $stmt->execute();
    }

    /**
     * Deleta um gasto
     */
    public function deletar($id_gasto, $id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM gastos WHERE id_gasto = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id_gasto, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Adiciona gasto recorrente
     */
    public function adicionarRecorrente($id_usuario, $id_categoria, $descricao, $valor, $dia_vencimento, $tipo = 'mensal', $quantidade = null)
    {
        if ($tipo === 'parcelado') {
            $sql = "INSERT INTO gastos_recorrentes (id_usuario, id_categoria, descricao, valor, dia_vencimento, tipo, quantidade_meses)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iisdisi", $id_usuario, $id_categoria, $descricao, $valor, $dia_vencimento, $tipo, $quantidade);
        } else {
            $sql = "INSERT INTO gastos_recorrentes (id_usuario, id_categoria, descricao, valor, dia_vencimento, tipo)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iisdis", $id_usuario, $id_categoria, $descricao, $valor, $dia_vencimento, $tipo);
        }
        return $stmt->execute();
    }

    /**
     * Obtém gastos recorrentes
     */
    public function getRecorrentes($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT r.*, c.nome as categoria FROM gastos_recorrentes r JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id_usuario = ? AND r.ativo = 1");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtém gastos de uma categoria específica
     */
    public function getGastosByCategoria($id_categoria)
    {
        $sql = "SELECT * FROM gastos WHERE id_categoria = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gera gastos recorrentes do mês
     */
    public function gerarRecorrentes($id_usuario)
    {
        $hoje = date('Y-m-d');
        $dia_hoje = date('d');
        $stmt = $this->db->prepare("SELECT * FROM gastos_recorrentes WHERE ativo = 1 AND id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($r = $result->fetch_assoc()) {
            $mes_atual = date('Y-m');
            $ultima = $r['ultima_execucao'] ? date('Y-m', strtotime($r['ultima_execucao'])) : null;
            
            if ($mes_atual == $ultima) continue;

            if ($dia_hoje >= $r['dia_vencimento']) {
                $data = date('Y-m') . '-' . str_pad($r['dia_vencimento'], 2, '0', STR_PAD_LEFT);
                
                // Adiciona o gasto na tabela principal
                $this->adicionar($r['id_usuario'], $r['id_categoria'], $r['descricao'], $r['valor'], $data);

                // Atualiza recorrência
                if ($r['tipo'] === 'parcelado') {
                    $novaQtd = $r['quantidade_meses'] - 1;
                    if ($novaQtd <= 0) {
                        $stmtFim = $this->db->prepare("UPDATE gastos_recorrentes SET ativo = 0, quantidade_meses = 0 WHERE id = ?");
                        $stmtFim->bind_param("i", $r['id']);
                        $stmtFim->execute();
                    } else {
                        $stmtQtd = $this->db->prepare("UPDATE gastos_recorrentes SET quantidade_meses = ? WHERE id = ?");
                        $stmtQtd->bind_param("ii", $novaQtd, $r['id']);
                        $stmtQtd->execute();
                    }
                }

                $stmtUpdate = $this->db->prepare("UPDATE gastos_recorrentes SET ultima_execucao = ? WHERE id = ?");
                $stmtUpdate->bind_param("si", $hoje, $r['id']);
                $stmtUpdate->execute();
            }
        }
    }
    /**
     * Deleta um gasto recorrente
     */
    public function deletarRecorrente($id, $id_usuario)
    {
        $stmt = $this->db->prepare("DELETE FROM gastos_recorrentes WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id, $id_usuario);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    /**
     * Atualiza um gasto recorrente
     */
    public function atualizarRecorrente($id, $id_usuario, $descricao, $valor, $dia_vencimento)
    {
        $stmt = $this->db->prepare("UPDATE gastos_recorrentes SET descricao = ?, valor = ?, dia_vencimento = ? WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("sdiii", $descricao, $valor, $dia_vencimento, $id, $id_usuario);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    /**
     * Obtém um gasto recorrente por ID
     */
    public function getRecorrenteById($id, $id_usuario)
    {
        $stmt = $this->db->prepare("SELECT r.*, c.nome as categoria FROM gastos_recorrentes r JOIN categorias c ON r.id_categoria = c.id_categoria WHERE r.id = ? AND r.id_usuario = ?");
        $stmt->bind_param("ii", $id, $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

}
