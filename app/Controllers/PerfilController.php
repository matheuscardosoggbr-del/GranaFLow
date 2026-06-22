<?php

namespace App\Controllers;

use App\Core\Controller;

class PerfilController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }

    public function index()
    {
        $userModel = $this->model('User');
        $usuario = $userModel->getById($_SESSION['id_usuario']);

        $data = [
            'csrf_token' => $this->gerarTokenCSRF(),
            'usuario' => $usuario,
            'nome_usuario' => $usuario['nome'] ?? ($_SESSION['nome'] ?? 'Usuário'),
        ];

        $this->view('perfil/index', $data);
    }

    public function atualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('perfil');
        }

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = 'Solicitação inválida.';
            redirecionar('perfil');
        }

        $id_usuario = (int) $_SESSION['id_usuario'];
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (strlen($nome) < 3 || strlen($nome) > 30) {
            $_SESSION['erro'] = 'O nome deve ter entre 3 e 30 caracteres.';
            redirecionar('perfil');
        }

        if (!$this->validarEmail($email)) {
            $_SESSION['erro'] = 'Informe um e-mail válido.';
            redirecionar('perfil');
        }

        $userModel = $this->model('User');
        $atual = $userModel->getById($id_usuario);
        if (!$atual) {
            $_SESSION['erro'] = 'Usuário não encontrado.';
            redirecionar('perfil');
        }

        if ($userModel->atualizarPerfil($id_usuario, $nome, $email, null)) {
            $_SESSION['sucesso'] = 'Perfil atualizado com sucesso.';
        } else {
            $_SESSION['erro'] = 'Não foi possível atualizar o perfil.';
        }

        redirecionar('perfil');
    }

    public function senha()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('perfil');
        }

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = 'Solicitação inválida.';
            redirecionar('perfil');
        }

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $senhaNova = $_POST['senha_nova'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (strlen($senhaNova) < 6) {
            $_SESSION['erro'] = 'A nova senha deve ter pelo menos 6 caracteres.';
            redirecionar('perfil');
        }

        if ($senhaNova !== $confirmar) {
            $_SESSION['erro'] = 'As senhas não coincidem.';
            redirecionar('perfil');
        }

        $userModel = $this->model('User');
        if ($userModel->atualizarSenha((int) $_SESSION['id_usuario'], $senhaAtual, $senhaNova)) {
            $_SESSION['sucesso'] = 'Senha atualizada com sucesso.';
        } else {
            $_SESSION['erro'] = 'Senha atual incorreta.';
        }

        redirecionar('perfil');
    }
}
