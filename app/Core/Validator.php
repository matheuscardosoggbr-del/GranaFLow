<?php

namespace App\Core;
class Validator
{
    private static $errors = [];
public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
public static function validarNumero($valor)
    {
        return is_numeric($valor) || preg_match('/^\d+(\,\d{2})?$/', $valor);
    }
public static function validarData($data)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $data);
        return $d && $d->format('Y-m-d') === $data;
    }
public static function validarSenha($senha)
    {
        if (strlen($senha) < 8) {
            return false;
        }
        if (!preg_match('/[0-9]/', $senha)) {
            return false;
        }
        if (!preg_match('/[a-z]/', $senha)) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            return false;
        }
        return true;
    }
public static function sanitizar($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizar'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
public static function validarTexto($valor, $minimo = 1, $maximo = 255)
    {
        $valor = trim($valor);
        $comprimento = strlen($valor);
        return $comprimento >= $minimo && $comprimento <= $maximo;
    }
public static function validarTelefone($telefone)
    {
        $telefone = preg_replace('/\D/', '', $telefone);
        return strlen($telefone) >= 10 && strlen($telefone) <= 11;
    }
public static function validarCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        return strlen($cpf) === 11 && ctype_digit($cpf);
    }
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
private static function aplicarValidacao($campo, $valor, $validacao)
    {
        if ($validacao !== 'required' && empty($valor)) {
            return;
        }

        if ($validacao === 'required') {
            if (empty($valor)) {
                self::$errors[$campo] = "O campo $campo Ã© obrigatÃ³rio.";
            }
        } elseif ($validacao === 'email') {
            if (!self::validarEmail($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser um email vÃ¡lido.";
            }
        } elseif ($validacao === 'numeric') {
            if (!self::validarNumero($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser um nÃºmero.";
            }
        } elseif ($validacao === 'date') {
            if (!self::validarData($valor)) {
                self::$errors[$campo] = "O campo $campo deve ser uma data vÃ¡lida (YYYY-MM-DD).";
            }
        } elseif ($validacao === 'password') {
            if (!self::validarSenha($valor)) {
                self::$errors[$campo] = "A senha deve ter mÃ­nimo 8 caracteres, nÃºmeros, maiÃºsculas e minÃºsculas.";
            }
        } elseif (preg_match('/^min:(\d+)$/', $validacao, $matches)) {
            if (strlen($valor) < $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ter no mÃ­nimo {$matches[1]} caracteres.";
            }
        } elseif (preg_match('/^max:(\d+)$/', $validacao, $matches)) {
            if (strlen($valor) > $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ter no mÃ¡ximo {$matches[1]} caracteres.";
            }
        } elseif (preg_match('/^min_value:(\d+)$/', $validacao, $matches)) {
            if ((float)$valor < $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ser no mÃ­nimo {$matches[1]}.";
            }
        } elseif (preg_match('/^max_value:(\d+)$/', $validacao, $matches)) {
            if ((float)$valor > $matches[1]) {
                self::$errors[$campo] = "O campo $campo deve ser no mÃ¡ximo {$matches[1]}.";
            }
        }
    }
public static function getErros()
    {
        return self::$errors;
    }
public static function getErro($campo)
    {
        return self::$errors[$campo] ?? null;
    }
public static function limparErros()
    {
        self::$errors = [];
    }
public static function temErros()
    {
        return !empty(self::$errors);
    }
}

