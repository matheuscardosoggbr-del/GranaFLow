<?php

namespace App\Models;

use App\Core\Model;

class Salario extends Model
{
    /**
     * Obtém o salário do usuário
     */
    public function getSalario($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT valor FROM salarios WHERE id_usuario = ? LIMIT 1");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? floatval($row['valor']) : 0.0;
    }

    /**
     * Define ou atualiza o salário
     */
    public function setSalario($id_usuario, $valor)
    {
        // Verificar se já existe
        $check = $this->db->prepare("SELECT id FROM salarios WHERE id_usuario = ?");
        $check->bind_param("i", $id_usuario);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            // Atualizar
            $stmt = $this->db->prepare("UPDATE salarios SET valor = ? WHERE id_usuario = ?");
            $stmt->bind_param("di", $valor, $id_usuario);
        } else {
            // Inserir
            $stmt = $this->db->prepare("INSERT INTO salarios (id_usuario, valor) VALUES (?, ?)");
            $stmt->bind_param("id", $id_usuario, $valor);
        }
        return $stmt->execute();
    }

    /**
     * Alias para setSalario (compatibilidade)
     */
    public function salvar($valor, $id_usuario)
    {
        return $this->setSalario($id_usuario, $valor);
    }

    /**
     * Obtém histórico de salários
     */
    public function getHistorico($id_usuario, $limitar = null)
    {
        $sql = "SELECT *, DATE_FORMAT(data_atualizacao, '%d/%m/%Y') as data_formatada 
                FROM salarios 
                WHERE id_usuario = ? 
                ORDER BY data_atualizacao DESC";
        
        if ($limitar) {
            $sql .= " LIMIT ?";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($limitar) {
            $stmt->bind_param("ii", $id_usuario, $limitar);
        } else {
            $stmt->bind_param("i", $id_usuario);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
