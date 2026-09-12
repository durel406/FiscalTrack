@echo off
REM A lancer apres avoir redemarre MySQL dans XAMPP (Start MySQL)
cd /d "%~dp0"
set PATH=C:\xampp\php;C:\xampp\mysql\bin;%PATH%
mysql -u root -e "CREATE DATABASE IF NOT EXISTS fiscaltrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
echo.
echo Comptes de test :
echo   admin@fiscaltrack.test / password
echo   comptable@fiscaltrack.test / password
echo   fiscal@fiscaltrack.test / password
pause
