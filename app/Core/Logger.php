<?php

namespace App\Core;
class Logger
{
    private static $diretorio_logs = '';
    private static $arquivo_info = 'app.log';
    private static $arquivo_erros = 'errors.log';
    private static $arquivo_seguranca = 'security.log';
public static function inicializar($diretorio = 'logs')
    {
        self::$diretorio_logs = $diretorio;
        
        if (!is_dir(self::$diretorio_logs)) {
            mkdir(self::$diretorio_logs, 0755, true);
        }
    }
public static function info($mensagem, $contexto = [])
    {
        self::escreverLog(self::$arquivo_info, 'INFO', $mensagem, $contexto);
    }
public static function erro($mensagem, $contexto = [])
    {
        self::escreverLog(self::$arquivo_erros, 'ERROR', $mensagem, $contexto);
    }
public static function aviso($mensagem, $contexto = [])
    {
        self::escreverLog(self::$arquivo_info, 'WARNING', $mensagem, $contexto);
    }
public static function seguranca($mensagem, $contexto = [])
    {
        self::escreverLog(self::$arquivo_seguranca, 'SECURITY', $mensagem, $contexto);
    }
public static function excecao(\Exception $e)
    {
        $contexto = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        self::escreverLog(self::$arquivo_erros, 'EXCEPTION', $e->getMessage(), $contexto);
    }
private static function escreverLog($arquivo, $nivel, $mensagem, $contexto = [])
    {
        if (empty(self::$diretorio_logs)) {
            self::inicializar();
        }

        $caminho_arquivo = self::$diretorio_logs . '/' . date('Y-m-d') . '_' . $arquivo;
        $timestamp = date('Y-m-d H:i:s');
        $id_usuario = $_SESSION['id_usuario'] ?? 'anÃ´nimo';
        $ip = self::obterIPUsuario();
        
        $contexto_json = !empty($contexto) ? json_encode($contexto) : '';
        $linha_log = "[{$timestamp}] [{$nivel}] [UsuÃ¡rio: {$id_usuario}] [IP: {$ip}] {$mensagem}";
        
        if (!empty($contexto_json)) {
            $linha_log .= " | Contexto: {$contexto_json}";
        }
        
        $linha_log .= "\n";
        
        file_put_contents($caminho_arquivo, $linha_log, FILE_APPEND);
    }
private static function obterIPUsuario()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
        }
        return $ip;
    }
public static function listarLogs()
    {
        if (!is_dir(self::$diretorio_logs)) {
            return [];
        }
        
        $arquivos = scandir(self::$diretorio_logs);
        return array_filter($arquivos, fn($f) => $f !== '.' && $f !== '..');
    }
public static function lerLog($arquivo, $linhas = 100)
    {
        $caminho = self::$diretorio_logs . '/' . $arquivo;
        
        if (!file_exists($caminho)) {
            return [];
        }
        
        $conteudo = file_get_contents($caminho);
        $linhas_array = array_slice(explode("\n", $conteudo), -$linhas);
        
        return array_filter($linhas_array);
    }
public static function limparLogsAntigos($dias = 30)
    {
        if (!is_dir(self::$diretorio_logs)) {
            return;
        }
        
        $tempo_limite = time() - ($dias * 24 * 60 * 60);
        $arquivos = scandir(self::$diretorio_logs);
        
        foreach ($arquivos as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..') {
                continue;
            }
            
            $caminho = self::$diretorio_logs . '/' . $arquivo;
            if (filemtime($caminho) < $tempo_limite) {
                unlink($caminho);
            }
        }
    }
}
Logger::inicializar();

