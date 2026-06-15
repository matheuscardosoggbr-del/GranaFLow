<?php

namespace App\Core;

/**
 * Classe de Cache
 * Armazena dados em cache para melhorar performance
 */
class Cache
{
    private $diretorio = 'storage/cache/';
    private $prefixo = '';

    /**
     * Construtor
     * 
     * @param string $prefixo Prefixo para chaves de cache
     */
    public function __construct($prefixo = 'app')
    {
        $this->prefixo = $prefixo;

        if (!is_dir($this->diretorio)) {
            mkdir($this->diretorio, 0755, true);
        }
    }

    /**
     * Obtém valor do cache
     * 
     * @param string $chave Chave do cache
     * @return mixed|null Valor do cache ou null se expirado/não existe
     */
    public function get($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (!file_exists($arquivo)) {
            return null;
        }

        $dados = json_decode(file_get_contents($arquivo), true);

        // Verificar expiração
        if ($dados['expiracao'] && $dados['expiracao'] < time()) {
            $this->remover($chave);
            return null;
        }

        return $dados['valor'];
    }

    /**
     * Armazena valor em cache
     * 
     * @param string $chave Chave do cache
     * @param mixed $valor Valor a ser armazenado
     * @param int $minutos Tempo de expiração em minutos (0 = sem expiração)
     */
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

    /**
     * Obtém valor do cache ou executa callback e armazena
     * 
     * @param string $chave Chave do cache
     * @param callable $callback Função a executar se não houver cache
     * @param int $minutos Tempo de expiração em minutos
     * @return mixed Valor do cache ou resultado do callback
     */
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

    /**
     * Remove item do cache
     * 
     * @param string $chave Chave do cache
     */
    public function remover($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (file_exists($arquivo)) {
            unlink($arquivo);
            return true;
        }

        return false;
    }

    /**
     * Limpa todo o cache
     */
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

    /**
     * Remove itens expirados do cache
     */
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

    /**
     * Verifica se chave existe e está válida
     * 
     * @param string $chave Chave do cache
     * @return bool
     */
    public function existe($chave)
    {
        return $this->get($chave) !== null;
    }

    /**
     * Obtém informações sobre um item do cache
     */
    public function info($chave)
    {
        $arquivo = $this->obterCaminhoArquivo($chave);

        if (!file_exists($arquivo)) {
            return null;
        }

        return json_decode(file_get_contents($arquivo), true);
    }

    /**
     * Incrementa um valor numérico no cache
     * 
     * @param string $chave Chave do cache
     * @param int $quantidade Quantidade a incrementar
     */
    public function incrementar($chave, $quantidade = 1)
    {
        $valor = (int)$this->get($chave);
        $novo_valor = $valor + $quantidade;
        $this->set($chave, $novo_valor);

        return $novo_valor;
    }

    /**
     * Decrementa um valor numérico no cache
     * 
     * @param string $chave Chave do cache
     * @param int $quantidade Quantidade a decrementar
     */
    public function decrementar($chave, $quantidade = 1)
    {
        return $this->incrementar($chave, -$quantidade);
    }

    /**
     * Obtém o caminho do arquivo de cache
     */
    private function obterCaminhoArquivo($chave)
    {
        $chave_hash = md5($this->prefixo . ':' . $chave);
        return $this->diretorio . $chave_hash . '.cache';
    }

    /**
     * Estatísticas do cache
     */
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

    /**
     * Formata tamanho em bytes para formato legível
     */
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
