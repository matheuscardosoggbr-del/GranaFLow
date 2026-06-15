<?php

namespace App\Core;

/**
 * Classe de Autenticação e Autorização
 * Gerencia verificação de sessão e acesso a recursos
 */
class Auth
{
    /**
     * Verifica se o usuário está autenticado
     * Se não, redireciona para login
     */
    public static function verificarAutenticacao()
    {
        if (!self::isLogado()) {
            session_destroy();
            redirecionar('auth/login');
            exit;
        }
    }

    /**
     * Verifica se o usuário tem permissões específicas
     * 
     * @param array $permissoes Array com permissões necessárias
     */
    public static function verificarAcesso($permissoes = [])
    {
        self::verificarAutenticacao();
        
        if (!empty($permissoes)) {
            $userPermissoes = $_SESSION['permissoes'] ?? [];
            $temAcesso = false;
            
            foreach ($permissoes as $permissao) {
                if (in_array($permissao, $userPermissoes)) {
                    $temAcesso = true;
                    break;
                }
            }
            
            if (!$temAcesso) {
                $_SESSION['erro'] = 'Acesso negado. Você não tem permissão para acessar este recurso.';
                redirecionar('dashboard');
                exit;
            }
        }
    }

    /**
     * Verifica se o usuário está logado
     */
    public static function isLogado()
    {
        return isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
    }

    /**
     * Obtém o ID do usuário logado
     */
    public static function getIdUsuario()
    {
        return $_SESSION['id_usuario'] ?? null;
    }

    /**
     * Obtém dados do usuário logado
     */
    public static function getUsuario()
    {
        return [
            'id_usuario' => $_SESSION['id_usuario'] ?? null,
            'nome' => $_SESSION['nome'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'permissoes' => $_SESSION['permissoes'] ?? []
        ];
    }

    /**
     * Faz logout do usuário
     */
    public static function logout()
    {
        Logger::info('Usuário realizado logout', ['id_usuario' => $_SESSION['id_usuario'] ?? null]);
        session_destroy();
        redirecionar('auth/login');
        exit;
    }

    /**
     * Realiza login (deve ser chamado pelo AuthController)
     */
    public static function login($id_usuario, $nome, $email, $permissoes = [])
    {
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['permissoes'] = $permissoes;
        $_SESSION['login_time'] = time();
        
        Logger::info('Usuário realizado login com sucesso', ['id_usuario' => $id_usuario, 'email' => $email]);
    }

    /**
     * Regenera o ID da sessão (para segurança)
     */
    public static function regenerarSessao()
    {
        session_regenerate_id(true);
    }
}
