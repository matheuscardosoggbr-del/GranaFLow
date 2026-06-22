<?php

namespace App\Core;

use mysqli;

class Model
{
    protected $db;

    public function __construct()
    {
        $host = env('DB_HOST', 'localhost');
        $usuario = env('DB_USER', 'root');
        $senha = env('DB_PASS', '');
        $banco = env('DB_NAME', 'granaflow');
        $porta = (int) env('DB_PORT', 3308);

        $this->db = new mysqli($host, $usuario, $senha, $banco, $porta);

        if ($this->db->connect_error) {
            throw new \RuntimeException(
                "Erro ao conectar ao banco de dados: " . $this->db->connect_error .
                " (Verifique se o MySQL estÃ¡ rodando na porta $porta)"
            );
        }

        $this->db->set_charset("utf8mb4");
    }
public function pertenceAoUsuario($tabela, $id_coluna, $id_recurso, $id_usuario)
    {
        $sql = "SELECT id_usuario FROM $tabela WHERE $id_coluna = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_recurso);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result && $result['id_usuario'] == $id_usuario;
    }
public function getErro()
    {
        return $this->db->error;
    }
}

