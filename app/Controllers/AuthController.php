<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['id_usuario'])) {
            redirecionar('dashboard');
        }

        $data = ['csrf_token' => $this->gerarTokenCSRF()];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar CSRF
            if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                $data['erro'] = "Solicitação inválida.";
                $this->view('auth/login', $data);
                return;
            }

            $email = $this->sanitizar($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            // Validar entrada
            if (empty($email) || empty($senha)) {
                $data['erro'] = "E-mail e senha são obrigatórios.";
                $this->view('auth/login', $data);
                return;
            }

            if (!$this->validarEmail($email)) {
                $data['erro'] = "E-mail inválido.";
                $this->view('auth/login', $data);
                return;
            }

            $userModel = $this->model('User');
            if ($userModel->verificarLogin($email, $senha)) {
                redirecionar('dashboard');
            } else {
                $data['erro'] = "E-mail ou senha incorretos.";
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login', $data);
        }
    }

    public function cadastro()
    {
        if (isset($_SESSION['id_usuario'])) {
            redirecionar('dashboard');
        }

        $data = ['csrf_token' => $this->gerarTokenCSRF()];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar CSRF
            if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                $data['erro'] = "Solicitação inválida.";
                $this->view('auth/cadastro', $data);
                return;
            }

            $nome = $this->sanitizar($_POST['nome'] ?? '');
            $email = $this->sanitizar($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $confirmarSenha = $_POST['confirmar_senha'] ?? '';

            // Validar entrada
            if (empty($nome) || empty($email) || empty($senha) || empty($confirmarSenha)) {
                $data['erro'] = "Todos os campos são obrigatórios.";
                $this->view('auth/cadastro', $data);
                return;
            }

            if (strlen($nome) < 3 || strlen($nome) > 30) {
                $data['erro'] = "Nome deve ter entre 3 e 30 caracteres.";
                $this->view('auth/cadastro', $data);
                return;
            }

            if (!$this->validarEmail($email)) {
                $data['erro'] = "E-mail inválido.";
                $this->view('auth/cadastro', $data);
                return;
            }

            if (strlen($senha) < 6) {
                $data['erro'] = "Senha deve ter pelo menos 6 caracteres.";
                $this->view('auth/cadastro', $data);
                return;
            }

            if ($senha !== $confirmarSenha) {
                $data['erro'] = "As senhas não coincidem.";
                $this->view('auth/cadastro', $data);
                return;
            }

            $userModel = $this->model('User');
            if ($userModel->registrar($nome, $email, $senha)) {
                redirecionar('auth/login?status=sucesso');
            } else {
                $data['erro'] = "Erro ao cadastrar usuário. E-mail pode já estar em uso.";
                $this->view('auth/cadastro', $data);
            }
        } else {
            $this->view('auth/cadastro', $data);
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        redirecionar('auth/login');
    }
}
