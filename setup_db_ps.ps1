# ===========================================
# Script para Configurar Banco GranaFlow
# ===========================================

$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$sqlFile = "C:\xampp\htdocs\GranaFLow\sql\banco_completo.sql"

# Verificar se arquivo SQL existe
if (-not (Test-Path $sqlFile)) {
    Write-Host "ERRO: Arquivo SQL não encontrado: $sqlFile" -ForegroundColor Red
    exit 1
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Configurando Banco de Dados GranaFlow" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Função para tentar conectar e executar
function Try-ExecuteSQL {
    param(
        [string]$Host,
        [string]$Port,
        [string]$Protocol
    )
    
    Write-Host "Tentando: $Host (porta: $Port)" -ForegroundColor Yellow
    
    try {
        $sqlContent = Get-Content $sqlFile -Raw
        $process = [System.Diagnostics.Process]::new()
        $process.StartInfo.FileName = $mysqlPath
        $process.StartInfo.UseShellExecute = $false
        $process.StartInfo.RedirectStandardInput = $true
        $process.StartInfo.RedirectStandardOutput = $true
        $process.StartInfo.RedirectStandardError = $true
        $process.StartInfo.CreateNoWindow = $true
        
        if ($Protocol) {
            $process.StartInfo.Arguments = "-h $Host -u root --protocol=$Protocol --port=$Port"
        } else {
            $process.StartInfo.Arguments = "-h $Host -u root --port=$Port"
        }
        
        $process.Start()
        $process.StandardInput.Write($sqlContent)
        $process.StandardInput.Close()
        
        $output = $process.StandardOutput.ReadToEnd()
        $error = $process.StandardError.ReadToEnd()
        
        $process.WaitForExit()
        
        if ($process.ExitCode -eq 0) {
            Write-Host "✅ Sucesso com $Host (porta $Port)" -ForegroundColor Green
            return $true
        } else {
            if ($error) {
                Write-Host "❌ Erro: $error" -ForegroundColor Red
            }
            return $false
        }
    }
    catch {
        Write-Host "❌ Exceção: $_" -ForegroundColor Red
        return $false
    }
}

# Tentar diferentes formas de conexão
$sucesso = $false

# Tentar 1: localhost TCP
if (Try-ExecuteSQL "localhost" 3306 "TCP") { $sucesso = $true }

# Tentar 2: 127.0.0.1 TCP
if (-not $sucesso) {
    if (Try-ExecuteSQL "127.0.0.1" 3306 "TCP") { $sucesso = $true }
}

# Tentar 3: localhost sem protocol
if (-not $sucesso) {
    if (Try-ExecuteSQL "localhost" 3306 "") { $sucesso = $true }
}

# Resultado final
Write-Host "`n========================================" -ForegroundColor Cyan

if ($sucesso) {
    Write-Host "✅ Banco de dados foi configurado com sucesso!" -ForegroundColor Green
    Write-Host "`nAcesse: http://localhost/GranaFLow/public/" -ForegroundColor Cyan
    Write-Host "========================================`n" -ForegroundColor Cyan
    exit 0
} else {
    Write-Host "❌ Falha ao configurar banco de dados" -ForegroundColor Red
    Write-Host "`nDicas:" -ForegroundColor Yellow
    Write-Host "1. Verifique se MySQL está rodando (XAMPP deve estar iniciado)"
    Write-Host "2. Verifique porta: netstat -ano | findstr 3306"
    Write-Host "3. Use phpMyAdmin: http://localhost/phpmyadmin"
    Write-Host "4. Importe manualmente: sql/banco_completo.sql"
    Write-Host "========================================`n" -ForegroundColor Cyan
    exit 1
}
