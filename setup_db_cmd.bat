@echo off
REM ===========================================
REM Script para Configurar Banco GranaFlow
REM Tenta 3 formas diferentes
REM ===========================================

echo.
echo ========================================
echo Configurando Banco de Dados GranaFlow
echo ========================================
echo.

cd /d "C:\xampp\mysql\bin"

REM Tentar sem senha
echo Tentativa 1: Sem senha...
mysql -u root < "C:\xampp\htdocs\GranaFLow\sql\banco_completo.sql" >nul 2>&1

if %errorlevel% equ 0 (
    echo [OK] Banco configurado com sucesso (sem senha)
    goto sucesso
)

REM Tentar com socket local
echo Tentativa 2: Com socket local...
mysql -u root --protocol=TCP --port=3306 < "C:\xampp\htdocs\GranaFLow\sql\banco_completo.sql" >nul 2>&1

if %errorlevel% equ 0 (
    echo [OK] Banco configurado com sucesso (socket TCP)
    goto sucesso
)

REM Tentar com 127.0.0.1
echo Tentativa 3: Com 127.0.0.1...
mysql -h 127.0.0.1 -u root --port=3306 < "C:\xampp\htdocs\GranaFLow\sql\banco_completo.sql" >nul 2>&1

if %errorlevel% equ 0 (
    echo [OK] Banco configurado com sucesso (127.0.0.1)
    goto sucesso
)

echo.
echo [ERRO] Nenhuma tentativa funcionou
echo.
echo Verificar:
echo 1. MySQL está rodando? (Abra XAMPP)
echo 2. Verifique porta: netstat -ano ^| findstr 3306
echo 3. Use phpMyAdmin: http://localhost/phpmyadmin
echo.
pause
exit /b 1

:sucesso
echo.
echo ========================================
echo Sucesso! Banco configurado.
echo ========================================
echo.
echo Acesse: http://localhost/GranaFLow/public/
echo.
pause
exit /b 0
