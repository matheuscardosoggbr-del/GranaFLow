<?php

namespace App\Models;

use App\Core\Model;

class Salario extends Model
{
public function getSalario($id_usuario)
    {
        $stmt = $this->db->prepare("SELECT valor FROM salarios WHERE id_usuario = ? LIMIT 1");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? floatval($row['valor']) : 0.0;
    }
public function setSalario($id_usuario, $valor)
    {
        $check = $this->db->prepare("SELECT id, valor FROM salarios WHERE id_usuario = ?");
        $check->bind_param("i", $id_usuario);
        $check->execute();
        $rowAtual = $check->get_result()->fetch_assoc();
        
        if ($rowAtual) {
            $historico = $this->db->prepare("INSERT INTO salarios_historico (id_usuario, valor) VALUES (?, ?)");
            $historico->bind_param("id", $id_usuario, $rowAtual['valor']);
            $historico->execute();
            $stmt = $this->db->prepare("UPDATE salarios SET valor = ? WHERE id_usuario = ?");
            $stmt->bind_param("di", $valor, $id_usuario);
        } else {
            $stmt = $this->db->prepare("INSERT INTO salarios (id_usuario, valor) VALUES (?, ?)");
            $stmt->bind_param("id", $id_usuario, $valor);
        }
        $sucesso = $stmt->execute();

        if ($sucesso && !$rowAtual) {
            $historico = $this->db->prepare("INSERT INTO salarios_historico (id_usuario, valor) VALUES (?, ?)");
            $historico->bind_param("id", $id_usuario, $valor);
            $historico->execute();
        }

        return $sucesso;
    }
public function salvar($valor, $id_usuario)
    {
        return $this->setSalario($id_usuario, $valor);
    }
public function getHistorico($id_usuario, $limitar = null)
    {
        $sql = "SELECT id, id_usuario, valor, data_registro,
                       DATE_FORMAT(data_registro, '%d/%m/%Y %H:%i') as data_formatada
                FROM salarios_historico
                WHERE id_usuario = ?
                ORDER BY data_registro DESC";
        
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

