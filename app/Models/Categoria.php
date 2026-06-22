<?php

namespace App\Models;

use App\Core\Model;

class Categoria extends Model
{
public function getCategorias($id_usuario)
    {
        $sql = "SELECT c.*, t.nome AS tipo_nome
                FROM categorias c
                JOIN tipos_categoria t ON c.id_tipo = t.id_tipo
                WHERE c.id_usuario = ? OR c.id_usuario IS NULL
                ORDER BY c.nome";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
public function getCategoriasPersonalizadas($id_usuario)
    {
        $sql = "SELECT c.*, t.nome AS tipo_nome
                FROM categorias c
                JOIN tipos_categoria t ON c.id_tipo = t.id_tipo
                WHERE c.id_usuario = ?
                ORDER BY c.nome";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
public function getCategoriaById($id)
    {
        $sql = "SELECT c.*, t.nome AS tipo_nome
                FROM categorias c
                JOIN tipos_categoria t ON c.id_tipo = t.id_tipo
                WHERE c.id_categoria = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
public function inserir($nome, $id_tipo, $id_usuario)
    {
        $sql = "INSERT INTO categorias (nome, id_tipo, id_usuario) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $nome, $id_tipo, $id_usuario);
        return $stmt->execute();
    }
public function atualizar($id_categoria, $nome, $id_tipo)
    {
        $sql = "UPDATE categorias SET nome = ?, id_tipo = ? WHERE id_categoria = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $nome, $id_tipo, $id_categoria);
        return $stmt->execute();
    }
public function deletar($id_categoria)
    {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id_categoria = ? AND id_usuario IS NOT NULL");
        $stmt->bind_param("i", $id_categoria);
        return $stmt->execute();
    }
}

