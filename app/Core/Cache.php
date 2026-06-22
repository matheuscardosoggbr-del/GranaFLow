<?php

namespace App\Core;
class Cache
{
    private $diretorio = 'storage/cache/';
    private $prefixo = '';
public function __construct($prefixo = 'app')
    {
        $this->prefixo = $prefixo;

        if (!is_dir($this->diretorio)) {
            mkdir($this->diretorio, 0755, true);
        }
    }
public function get($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (!file_exists($arquivo)) {
            return null;
        }

        $dados = json_decode(file_get_contents($arquivo), true);
        if ($dados['expiracao'] && $dados['expiracao'] < time()) {
            $this->remover($chave);
            return null;
        }

        return $dados['valor'];
    }
public function set($chave, $valor, $minutos = 60)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        $dados = [
            'valor' => $valor,
            'expiracao' => $minutos > 0 ? time() + ($minutos * 60) : 0,
            'criado_em' => time(),
            'chave' => $chave
        ];

        file_put_contents($arquivo, json_encode($dados));
        return true;
    }
public function remember($chave, $callback, $minutos = 60)
    {
        $valor = $this->get($chave);

        if ($valor !== null) {
            return $valor;
        }

        $valor = call_user_func($callback);
        $this->set($chave, $valor, $minutos);

        return $valor;
    }
public function remover($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (file_exists($arquivo)) {
            unlink($arquivo);
            return true;
        }

        return false;
    }
public function limpar()
    {
        if (!is_dir($this->diretorio)) {
            return true;
        }

        $arquivos = scandir($this->diretorio);

        foreach ($arquivos as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..') {
                continue;
            }

            $caminho = $this->diretorio . $arquivo;
            if (is_file($caminho)) {
                unlink($caminho);
            }
        }

        return true;
    }
public function limparExpirados()
    {
        if (!is_dir($this->diretorio)) {
            return;
        }

        $arquivos = scandir($this->diretorio);

        foreach ($arquivos as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..') {
                continue;
            }

            $caminho = $this->diretorio . $arquivo;
            $dados = json_decode(file_get_contents($caminho), true);

            if ($dados['expiracao'] && $dados['expiracao'] < time()) {
                unlink($caminho);
            }
        }
    }
public function existe($chave)
    {
        return $this->get($chave) !== null;
    }
public function info($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (!file_exists($arquivo)) {
            return null;
        }

        return json_decode(file_get_contents($arquivo), true);
    }
public function incrementar($chave, $quantidade = 1)
    {
        $valor = (int)$this->get($chave);
        $novo_valor = $valor + $quantidade;
        $this->set($chave, $novo_valor);

        return $novo_valor;
    }
public function decrementar($chave, $quantidade = 1)
    {
        return $this->incrementar($chave, -$quantidade);
    }
private function obterCaminhoArquivo($chave)
    {
        $chave_hash = md5($this->prefixo . ':' . $chave);
        return $this->diretorio . $chave_hash . '.cache';
    }
public function stats()
    {
        if (!is_dir($this->diretorio)) {
            return ['total' => 0, 'tamanho' => 0];
        }

        $arquivos = scandir($this->diretorio);
        $total = 0;
        $tamanho = 0;

        foreach ($arquivos as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..') {
                continue;
            }

            $caminho = $this->diretorio . $arquivo;
            $total++;
            $tamanho += filesize($caminho);
        }

        return [
            'total' => $total,
            'tamanho' => $tamanho,
            'tamanho_formatado' => $this->formatarTamanho($tamanho)
        ];
    }
private function formatarTamanho($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $tamanho = $bytes;
        $indice = 0;

        while ($tamanho >= 1024 && $indice < count($unidades) - 1) {
            $tamanho /= 1024;
            $indice++;
        }

        return round($tamanho, 2) . ' ' . $unidades[$indice];
    }
}

