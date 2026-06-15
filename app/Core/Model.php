<?php

namespace App\Core;

use mysqli;

class Model
{
    protected $db;

    public function __construct()
    {
        $host   = "localhost";
        $usuario = "root";
        $senha  = "";
        $banco  = "granaflow";
        $porta  = 3308;

        $this->db = new mysqli($host, $usuario, $senha, $banco, $porta);

        if ($this->db->connect_error) {
            die("Erro ao conectar ao banco de dados: " . $this->db->connect_error . " (Verifique se o MySQL está rodando na porta $porta)");
        }

        $this->db->set_charset("utf8mb4");
    }

    /**
     * Verifica se um recurso pertence ao usuário
     */
    public function pertenceAoUsuario($tabela, $id_coluna, $id_recurso, $id_usuario)
    {
        $sql = "SELECT id_usuario FROM $tabela WHERE $id_coluna = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_recurso);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result && $result['id_usuario'] == $id_usuario;
    }

    /**
     * Obtém a última mensagem de erro
     */
    public function getErro()
    {
        return $this->db->error;
    }
}
