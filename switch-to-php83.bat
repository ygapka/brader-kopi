@echo off
>nul 2>&1 "%SYSTEMROOT%\system32\cacls.exe" "%SYSTEMROOT%\system32\config\system"
if '%errorlevel%' NEQ '0' (
    echo Meminta izin Administrator...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

echo Mengatur PATH Windows ke PHP 8.3 (Laragon)...
powershell -NoProfile -ExecutionPolicy Bypass -Command "$machinePath = [Environment]::GetEnvironmentVariable('Path', 'Machine'); $parts = $machinePath -split ';' | Where-Object { $_.Trim() -ne '' -and $_ -notlike '*xampp\php*' -and $_ -notlike '*laragon\bin\php*' }; $newPath = 'C:\laragon\bin\php\php-8.3.35-Win32-vs16-x64;' + ($parts -join ';'); [Environment]::SetEnvironmentVariable('Path', $newPath, 'Machine')"

echo.
echo ========================================================
echo  BERHASIL! PHP default Windows telah diubah ke PHP 8.3!
echo ========================================================
echo.
echo Silakan buka terminal baru (PowerShell / CMD) dan ketik:
echo   php -v
echo.
pause

