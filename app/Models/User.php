<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
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
            $_SESSION["email"] = $user["email"];
            return true;
        }
        return false;
    }
}
