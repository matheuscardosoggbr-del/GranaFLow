<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function getById($id_usuario)
    {
        $sql = "SELECT id_usuario, nome, email, id_moeda, data_criacao, data_atualizacao FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function registrar($nome, $email, $senha, $id_moeda = 1)
    {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha, id_moeda) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $nome, $email, $senha_hash, $id_moeda);
        return $stmt->execute();
    }

    public function verificarLogin($email, $senha)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($senha, $user['senha'])) {
            session_regenerate_id(true);
            $_SESSION["id_usuario"] = $user["id_usuario"];
            $_SESSION["nome"] = $user["nome"];
            $_SESSION["nome_usuario"] = $user["nome"];
            $_SESSION["email"] = $user["email"];
            return true;
        }
        return false;
    }

    public function atualizarPerfil($id_usuario, $nome, $email, $id_moeda = null)
    {
        if ($id_moeda === null) {
            $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $id_usuario);
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, id_moeda = ? WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssii", $nome, $email, $id_moeda, $id_usuario);
        }

        $ok = $stmt->execute();
        if ($ok) {
            $_SESSION['nome'] = $nome;
            $_SESSION['nome_usuario'] = $nome;
            $_SESSION['email'] = $email;
        }
        return $ok;
    }

    public function atualizarSenha($id_usuario, $senhaAtual, $senhaNova)
    {
        $sql = "SELECT senha FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($senhaAtual, $user['senha'])) {
            return false;
        }

        $senha_hash = password_hash($senhaNova, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET senha = ? WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $senha_hash, $id_usuario);
        return $stmt->execute();
    }
}
