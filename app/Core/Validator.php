<?php

namespace App\Core;

/**
 * Classe de Validação
 * Fornece métodos para validar diferentes tipos de dados
 */
class Validator
{
    private static $errors = [];

    /**
     * Valida um email
     */
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida um número/valor
     */
    public static function validarNumero($valor)
    {
        return is_numeric($valor) || preg_match('/^\d+(\,\d{2})?$/', $valor);
    }

    /**
     * Valida uma data (formato YYYY-MM-DD)
     */
    public static function validarData($data)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $data);
        return $d && $d->format('Y-m-d') === $data;
    }

    /**
     * Valida força de senha
     * Exige: mín 8 caracteres, números, maiúsculas, minúsculas
     */
    public static function validarSenha($senha)
    {
        if (strlen($senha) < 8) {
            return false; // Mínimo 8 caracteres
        }
        if (!preg_match('/[0-9]/', $senha)) {
            return false; // Deve ter números
        }
        if (!preg_match('/[a-z]/', $senha)) {
            return false; // Deve ter minúsculas
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            return false; // Deve ter maiúsculas
        }
        return true;
    }

    /**
     * Sanitiza uma string (remove espaços e escapa HTML)
     */
    public static function sanitizar($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizar'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida um campo de texto
     */
    public static function validarTexto($valor, $minimo = 1, $maximo = 255)
    {
        $valor = trim($valor);
        $comprimento = strlen($valor);
        return $comprimento >= $minimo && $comprimento <= $maximo;
    }

    /**
     * Valida um campo de telefone (brasileiro)
     */
    public static function validarTelefone($telefone)
    {
        $telefone = preg_replace('/\D/', '', $telefone);
        return strlen($telefone) >= 10 && strlen($telefone) <= 11;
    }

    /**
     * Valida um campo de CPF (básico)
     */
    public static function validarCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        return strlen($cpf) === 11 && ctype_digit($cpf);
    }

    /**
     * Valida múltiplos campos conforme as regras especificadas
     * 
     * Exemplo de uso:
     * $regras = [
     *     'email' => 'required|email',
     *     'nome' => 'required|min:3|max:50',
     *     'senha' => 'required|password',
     *     'data' => 'date'
     * ];
     * Validator::validarCampos($_POST, $regras);
     */
    public static function validarCampos($dados, $regras)
    {
        self::$errors = [];
        
        foreach ($regras as $campo => $regraString) {
            $valor = $dados[$campo] ?? '';
            $validacoes = explode('|', $regraString);
            
            foreach ($validacoes as $validacao) {
                self::aplicarValidacao($campo, $valor, trim($validacao));
            }
        }
        
        return empty(self::$errors);
    }

    /**
     * Aplica uma validação específica
     */
    private static function aplicarValidacao($campo, $valor, $validacao)
    {
        // Pular validação se campo é opcional e vazio
        if ($validacao !== 'required' && empty($valor)) {
            return;
        }

        if ($validacao === 'required') {
            if (empty($valor)) {
                self::$errors[$campo] = "O campo $campo é obrigatório.";
            }
        } elseif ($validacao === 'email') {
            if (!self::validarEmail($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser um email válido.";
            }
        } elseif ($validacao === 'numeric') {
            if (!self::validarNumero($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser um número.";
            }
        } elseif ($validacao === 'date') {
            if (!self::validarData($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser uma data válida (YYYY-MM-DD).";
            }
        } elseif ($validacao === 'password') {
            if (!self::validarSenha($valor)) {
                self::$errors[$campo] = "A senha deve ter mínimo 8 caracteres, números, maiúsculas e minúsculas.";
            }
        } elseif (preg_match('/^min:(\d+)$/', $validacao, $matches)) {
            if (strlen($valor) < $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ter no mínimo {$matches[1]} caracteres.";
            }
        } elseif (preg_match('/^max:(\d+)$/', $validacao, $matches)) {
            if (strlen($valor) > $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ter no máximo {$matches[1]} caracteres.";
            }
        } elseif (preg_match('/^min_value:(\d+)$/', $validacao, $matches)) {
            if ((float)$valor < $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ser no mínimo {$matches[1]}.";
            }
        } elseif (preg_match('/^max_value:(\d+)$/', $validacao, $matches)) {
            if ((float)$valor > $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ser no máximo {$matches[1]}.";
            }
        }
    }

    /**
     * Obtém todos os erros de validação
     */
    public static function getErros()
    {
        return self::$errors;
    }

    /**
     * Obtém erro de um campo específico
     */
    public static function getErro($campo)
    {
        return self::$errors[$campo] ?? null;
    }

    /**
     * Limpa os erros armazenados
     */
    public static function limparErros()
    {
        self::$errors = [];
    }

    /**
     * Verifica se há erros de validação
     */
    public static function temErros()
    {
        return !empty(self::$errors);
    }
}
