<?php

namespace App\Core;

/**
 * Classe de Rate Limiting
 * Protege a aplicação contra ataques de força bruta
 */
class RateLimiter
{
    private $max_tentativas = 5;
    private $intervalo_minutos = 15;
    private $diretorio_armazenamento = 'storage/rate_limit';

    /**
     * Construtor
     * 
     * @param int $max_tentativas Máximo de tentativas permitidas
     * @param int $intervalo_minutos Intervalo de tempo para contar tentativas
     */
    public function __construct($max_tentativas = 5, $intervalo_minutos = 15)
    {
        $this->max_tentativas = $max_tentativas;
        $this->intervalo_minutos = $intervalo_minutos;
        
        if (!is_dir($this->diretorio_armazenamento)) {
            mkdir($this->diretorio_armazenamento, 0755, true);
        }
    }

    /**
     * Verifica se o identificador está dentro do limite
     * 
     * @param string $identificador Email, IP ou outro identificador único
     * @return bool true se está dentro do limite, false se foi excedido
     */
    public function verificarLimite($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $agora = time();

        // Limpar tentativas antigas
        $tentativas = array_filter($tentativas, function($tempo) use ($agora) {
            return ($agora - $tempo) < ($this->intervalo_minutos * 60);
        });

        // Verificar se excedeu limite
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

    /**
     * Registra uma tentativa
     * 
     * @param string $identificador Email, IP ou outro identificador único
     */
    public function registrarTentativa($identificador)
    {
        $tentativas = $this->obterTentativas($identificador);
        $tentativas[] = time();
        $this->salvarTentativas($identificador, $tentativas);
    }

    /**
     * Limpa todas as tentativas de um identificador
     * 
     * @param string $identificador Email, IP ou outro identificador único
     */
    public function limparTentativas($identificador)
    {
        $hash = hash('sha256', $identificador);
        $arquivo = $this->diretorio_armazenamento . '/' . $hash;
        
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }

    /**
     * Obtém o tempo de bloqueio restante em segundos
     * 
     * @param string $identificador Email, IP ou outro identificador único
     * @return int Segundos até desbloqueio, ou 0 se desbloqueado
     */
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

    /**
     * Obtém o número de tentativas restantes
     * 
     * @param string $identificador Email, IP ou outro identificador único
     * @return int Número de tentativas restantes
     */
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

    /**
     * Obter todas as tentativas registradas
     */
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

    /**
     * Salvar tentativas registradas
     */
    private function salvarTentativas($identificador, $tentativas)
    {
        $hash = hash('sha256', $identificador);
        $arquivo = $this->diretorio_armazenamento . '/' . $hash;
        file_put_contents($arquivo, json_encode($tentativas));
    }

    /**
     * Limpar dados antigos (mais antigos que $dias)
     */
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

    /**
     * Configurar limite de tentativas
     */
    public function setMaxTentativas($quantidade)
    {
        $this->max_tentativas = $quantidade;
    }

    /**
     * Configurar intervalo de tempo
     */
    public function setIntervaloMinutos($minutos)
    {
        $this->intervalo_minutos = $minutos;
    }
}
