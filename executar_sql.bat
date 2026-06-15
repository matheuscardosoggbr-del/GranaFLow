@echo off
REM Script para executar SQL do banco de dados
REM Execute este arquivo direto ou via PowerShell

cd /d "C:\xampp\mysql\bin"
mysql -u root -p"" < "C:\xampp\htdocs\GranaFLow\sql\banco_completo.sql"

if %errorlevel% equ 0 (
    echo.
    echo ===============================================
    echo Sucesso! Banco de dados foi configurado.
    echo ===============================================
    pause
) else (
    echo.
    echo ===============================================
    echo Erro ao executar SQL!
    echo ===============================================
    pause
)
