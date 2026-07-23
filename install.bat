@echo off
REM ============================================================
REM  INSTALL.BAT — Setup otomatis project VenueIn
REM  Jalankan file ini di dalam folder project (setelah git clone)
REM ============================================================

REM --- UBAH SESUAI KEBUTUHAN ---
set DB_NAME=venuein
set DB_USER=root
set DB_PASS=
set SQL_FILE=database_export.sql

echo.
echo [1/8] Install dependency PHP (Composer)...
call composer install
if %errorlevel% neq 0 goto :error

echo.
echo [2/8] Install dependency JS (NPM)...
call npm install
if %errorlevel% neq 0 goto :error

echo.
echo [3/8] Build asset frontend...
call npm run build
if %errorlevel% neq 0 goto :error

echo.
echo [4/8] Menyalin .env.example menjadi .env...
if not exist .env (
    copy .env.example .env
) else (
    echo File .env sudah ada, dilewati.
)

echo.
echo [5/8] Generate application key...
call php artisan key:generate

echo.
echo [6/8] Membuat database "%DB_NAME%" jika belum ada...
mysql -u%DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME%"
if %errorlevel% neq 0 goto :error

echo.
echo [7/8] Import file database dari "%SQL_FILE%"...
if exist "%SQL_FILE%" (
    mysql -u%DB_USER% -p%DB_PASS% %DB_NAME% < "%SQL_FILE%"
    if %errorlevel% neq 0 goto :error
) else (
    echo File %SQL_FILE% tidak ditemukan, langkah import dilewati.
    echo Jalankan "php artisan migrate" secara manual jika perlu.
)

echo.
echo [8/8] Membuat storage link...
call php artisan storage:link

echo.
echo ============================================================
echo  SELESAI! Jalankan "php artisan serve" untuk memulai server.
echo ============================================================
pause
exit /b 0

:error
echo.
echo ============================================================
echo  TERJADI ERROR — proses instalasi dihentikan.
echo  Periksa pesan error di atas.
echo ============================================================
pause
exit /b 1
