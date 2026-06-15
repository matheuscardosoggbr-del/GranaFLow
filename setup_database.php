<?php
/**
 * Configuração Avançada do Banco de Dados GranaFlow
 * Tenta múltiplas formas de conexão
 */

$tentativa_method = $_POST['method'] ?? 'auto';
$senha = $_POST['senha'] ?? '';
$port = $_POST['port'] ?? 3306;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Configurar Banco - GranaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .setup-card { max-width: 650px; background: white; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 40px; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .warning { background: #fff3e0; color: #e65100; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .info { background: #e3f2fd; color: #1565c0; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .method-btn { margin: 5px; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 8px; max-height: 300px; overflow-y: auto; font-size: 11px; }
    </style>
</head>
<body>
    <div class="setup-card">
        <h1 class="mb-3">🔧 Configurar Banco de Dados</h1>
        <p class="text-muted">GranaFlow v2.0</p>

        <?php
        // Função para tentar conectar
        function testar_conexao($host, $user, $pass, $port = 3306) {
            try {
                $conn = new mysqli($host, $user, $pass, '', $port);
                if ($conn->connect_error) {
                    return ['sucesso' => false, 'erro' => $conn->connect_error];
                }
                return ['sucesso' => true, 'conn' => $conn];
            } catch (Exception $e) {
                return ['sucesso' => false, 'erro' => $e->getMessage()];
            }
        }

        // Função para executar SQL
        function executar_sql($conn) {
            $sql_file = __DIR__ . '/sql/banco_completo.sql';
            if (!file_exists($sql_file)) {
                return ['sucesso' => false, 'erro' => 'Arquivo SQL não encontrado'];
            }

            $sql_content = file_get_contents($sql_file);
            $all_commands = explode(';', $sql_content);
            $commands = [];

            foreach ($all_commands as $cmd) {
                $cmd = trim($cmd);
                $lines = explode("\n", $cmd);
                $clean_lines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && substr($line, 0, 2) !== '--') {
                        $clean_lines[] = $line;
                    }
                }
                $clean_cmd = implode("\n", $clean_lines);
                if (!empty($clean_cmd)) {
                    $commands[] = $clean_cmd;
                }
            }

            $executed = 0;
            $errors = [];
            $log = "Processando " . count($commands) . " comandos SQL...\n\n";

            foreach ($commands as $i => $command) {
                if ($conn->multi_query($command)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->next_result());
                    $executed++;
                    $log .= "✅ " . ($i + 1) . "\n";
                } else {
                    if (stripos($conn->error, 'Duplicate key name') !== false || stripos($conn->error, 'Duplicate index') !== false) {
                        $log .= "⚠️ " . ($i + 1) . ": índice já existia, pulando.\n";
                        continue;
                    }

                    $errors[] = $conn->error;
                    $log .= "❌ " . ($i + 1) . ": " . $conn->error . "\n";
                }
            }

            return [
                'sucesso' => (count($errors) === 0 && $executed > 0),
                'executed' => $executed,
                'total' => count($commands),
                'errors' => $errors,
                'log' => $log
            ];
        }

        // Processar requisição
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<div class="info">⏳ Testando conexão...</div>';

            $resultado = null;

            if ($tentativa_method === 'auto' || $tentativa_method === 'localhost') {
                $resultado = testar_conexao('localhost', 'root', $senha, $port);
                if ($resultado['sucesso']) {
                    echo '<div class="success">✅ Conectado com: localhost</div>';
                    $sql_result = executar_sql($resultado['conn']);
                    if ($sql_result['sucesso']) {
                        echo '<div class="success"><strong>🎉 Sucesso Completo!</strong><br>
                        Banco de dados configurado com sucesso.<br>
                        ' . $sql_result['executed'] . ' de ' . $sql_result['total'] . ' comandos executados.</div>';
                        echo '<pre>' . htmlspecialchars($sql_result['log']) . '</pre>';
                        echo '<div style="margin-top: 20px;"><a href="http://localhost/GranaFLow/public/" class="btn btn-primary w-100">🚀 Ir para GranaFlow</a></div>';
                        $resultado['conn']->close();
                        exit;
                    } else {
                        echo '<div class="error"><strong>❌ Erro ao executar SQL</strong><br>' . implode('<br>', $sql_result['errors']) . '</div>';
                        echo '<pre>' . htmlspecialchars($sql_result['log']) . '</pre>';
                        $resultado['conn']->close();
                    }
                }
            }

            if ($tentativa_method === 'auto' || $tentativa_method === '127.0.0.1') {
                if (!$resultado || !$resultado['sucesso']) {
                    $resultado = testar_conexao('127.0.0.1', 'root', $senha, $port);
                    if ($resultado['sucesso']) {
                        echo '<div class="success">✅ Conectado com: 127.0.0.1</div>';
                        $sql_result = executar_sql($resultado['conn']);
                        if ($sql_result['sucesso']) {
                            echo '<div class="success"><strong>🎉 Sucesso!</strong></div>';
                            echo '<pre>' . htmlspecialchars($sql_result['log']) . '</pre>';
                            echo '<div style="margin-top: 20px;"><a href="http://localhost/GranaFLow/public/" class="btn btn-primary w-100">🚀 Ir para GranaFlow</a></div>';
                            $resultado['conn']->close();
                            exit;
                        }
                        $resultado['conn']->close();
                    }
                }
            }

            if (!$resultado || !$resultado['sucesso']) {
                echo '<div class="error"><strong>❌ Falha na conexão</strong><br>' . ($resultado['erro'] ?? 'Erro desconhecido') . '</div>';
                echo '<div class="warning"><strong>💡 Sugestões:</strong>
                <ul style="margin-bottom: 0; margin-top: 10px;">
                <li>Verifique se MySQL está rodando no XAMPP</li>
                <li>Tente mudar a porta (padrão: 3306)</li>
                <li>Use phpMyAdmin para testar conexão</li>
                </ul></div>';
            }
        }
        ?>

        <!-- Formulário -->
        <form method="POST" class="mt-4">
            <div class="form-group mb-3">
                <label class="form-label">🔐 Senha do MySQL (root)</label>
                <input type="password" name="senha" class="form-control" placeholder="Deixe vazio se não houver senha" value="<?php echo htmlspecialchars($senha); ?>">
                <small class="text-muted">No XAMPP padrão, geralmente não há senha</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">🔌 Porta MySQL</label>
                <input type="number" name="port" class="form-control" value="<?php echo $port; ?>" placeholder="3306">
                <small class="text-muted">Padrão: 3306</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">🎯 Método de Conexão</label>
                <div>
                    <button type="submit" name="method" value="auto" class="btn btn-primary method-btn">
                        🔄 Auto (localhost + 127.0.0.1)
                    </button>
                    <button type="submit" name="method" value="localhost" class="btn btn-secondary method-btn">
                        localhost
                    </button>
                    <button type="submit" name="method" value="127.0.0.1" class="btn btn-secondary method-btn">
                        127.0.0.1
                    </button>
                </div>
            </div>

            <hr>

            <div class="info">
                <strong>❓ Se nada funcionar:</strong>
                <ol style="margin-bottom: 0; margin-top: 10px;">
                    <li>Abra o phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">localhost/phpmyadmin</a></li>
                    <li>Verifique usuário e senha padrão</li>
                    <li>Importe manualmente o arquivo: <code>sql/banco_completo.sql</code></li>
                </ol>
            </div>
        </form>
    </div>
</body>
</html>
