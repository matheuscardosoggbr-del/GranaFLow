<?php

namespace App\Core;
class RateLimiter
{
    private $max_tentativas = 5;
    private $intervalo_minutos = 15;
    private $diretorio_armazenamento = 'storage/rate_limit';
public function __construct($max_tentativas = 5, $intervalo_minutos = 15)
    {
        $this->max_tentativas = $max_tentativas;
        $this->intervalo_minutos = $intervalo_minutos;
        
        if (!is_dir($this->diretorio_armazenamento)) {
            mkdir($this->diretorio_armazenamento, 0755, true);
        }
    }
public function verificarLimite($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $agora = time();
        $tentativas = array_filter($tentativas, function($tempo) use ($agora) {
            return ($agora - $tempo) < ($this->intervalo_minutos * 60);
        });
        if (count($tentativas) >= $this->max_tentativas) {
            Logger::seguranca('Limite de tentativas excedido', [
                'identificador' => hash('sha256', $identificador),
                'tentativas' => count($tentativas),
                'limite' => $this->max_tentativas
            ]);
            return false;
        }

        return true;
    }
public function registrarTentativa($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $tentativas[] = time();
        $this->salvarTentativas($identificador, $tentativas);
    }
public function limparTentativas($identificador)
    {
        $hash = hash('sha256', $identificador);
        $arquivo = $this->diretorio_armazenamento . '/' . $hash;
        
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }
public function obterTempoRestante($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $agora = time();
        $intervalo_segundos = $this->intervalo_minutos * 60;

        $tentativas_validas = array_filter($tentativas, function($tempo) use ($agora, $intervalo_segundos) {
            return ($agora - $tempo) < $intervalo_segundos;
        });

        if (count($tentativas_validas) >= $this->max_tentativas) {
            $tentativa_mais_antiga = min($tentativas_validas);
            $tempo_restante = $intervalo_segundos - ($agora - $tentativa_mais_antiga);
            return max(0, $tempo_restante);
        }

        return 0;
    }
public function obterTentativasRestantes($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $agora = time();
        $intervalo_segundos = $this->intervalo_minutos * 60;

        $tentativas_validas = array_filter($tentativas, function($tempo) use ($agora, $intervalo_segundos) {
            return ($agora - $tempo) < $intervalo_segundos;
        });

        return max(0, $this->max_tentativas - count($tentativas_validas));
    }
private function obterTentativas($identificador)
    {
        $hash = hash('sha256', $identificador);
        $arquivo = $this->diretorio_armazenamento . '/' . $hash;

        if (!file_exists($arquivo)) {
            return [];
        }

        $conteudo = file_get_contents($arquivo);
        return json_decode($conteudo, true) ?: [];
    }
private function salvarTentativas($identificador, $tentativas)
    {
        $hash = hash('sha256', $identificador);
        $arquivo = $this->diretorio_armazenamento . '/' . $hash;
        file_put_contents($arquivo, json_encode($tentativas));
    }
public function limparDadosAntigos($dias = 7)
    {
        $tempo_limite = time() - ($dias * 24 * 60 * 60);
        $arquivos = scandir($this->diretorio_armazenamento);

        foreach ($arquivos as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..') {
                continue;
            }

            $caminho = $this->diretorio_armazenamento . '/' . $arquivo;
            if (filemtime($caminho) < $tempo_limite) {
                unlink($caminho);
            }
        }
    }
public function setMaxTentativas($quantidade)
    {
        $this->max_tentativas = $quantidade;
    }
public function setIntervaloMinutos($minutos)
    {
        $this->intervalo_minutos = $minutos;
    }
}

