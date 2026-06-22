<?php

namespace App\Core;
class Auth
{
public static function verificarAutenticacao()
    {
        if (!self::isLogado()) {
            session_destroy();
            redirecionar('auth/login');
            exit;
        }
    }
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
                $_SESSION['erro'] = 'Acesso negado. VocÃª nÃ£o tem permissÃ£o para acessar este recurso.';
                redirecionar('dashboard');
                exit;
            }
        }
    }
public static function isLogado()
    {
        return isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
    }
public static function getIdUsuario()
    {
        return $_SESSION['id_usuario'] ?? null;
    }
public static function getUsuario()
    {
        return [
            'id_usuario' => $_SESSION['id_usuario'] ?? null,
            'nome' => $_SESSION['nome'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'permissoes' => $_SESSION['permissoes'] ?? []
        ];
    }
public static function logout()
    {
        Logger::info('UsuÃ¡rio realizado logout', ['id_usuario' => $_SESSION['id_usuario'] ?? null]);
        session_destroy();
        redirecionar('auth/login');
        exit;
    }
public static function login($id_usuario, $nome, $email, $permissoes = [])
    {
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['permissoes'] = $permissoes;
        $_SESSION['login_time'] = time();
        
        Logger::info('UsuÃ¡rio realizado login com sucesso', ['id_usuario' => $id_usuario, 'email' => $email]);
    }
public static function regenerarSessao()
    {
        session_regenerate_id(true);
    }
}

