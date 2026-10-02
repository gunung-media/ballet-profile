@echo off
REM ===== Jalankan website Angelique Ballet untuk uji coba lokal =====
REM Klik dua kali file ini. Tutup jendela ini untuk mematikan server.
cd /d "%~dp0"

where php >nul 2>nul
if %errorlevel%==0 (
  set PHP=php
) else if exist "C:\php8.3\php.exe" (
  set PHP=C:\php8.3\php.exe
) else (
  echo PHP tidak ditemukan. Pasang PHP 8.1+ atau XAMPP terlebih dahulu.
  pause
  exit /b 1
)

echo.
echo   Website : http://127.0.0.1:8000
echo   Setup   : http://127.0.0.1:8000/admin/setup.php   (pertama kali)
echo   Admin   : http://127.0.0.1:8000/admin
echo.
echo   Jangan tutup jendela ini selama uji coba. Tekan Ctrl+C untuk berhenti.
echo.

start "" "http://127.0.0.1:8000/admin/setup.php"
%PHP% -d upload_max_filesize=8M -d post_max_size=10M -S 127.0.0.1:8000
pause
