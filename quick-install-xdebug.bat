@echo off
echo ========================================
echo Xdebug Quick Install Helper
echo ========================================
echo.
echo This will help you download and install Xdebug
echo.
pause

echo.
echo Step 1: Opening download page...
start https://xdebug.org/files/php_xdebug-3.3.2-8.2-zts-vs16-x86_64.dll

echo.
echo Step 2: Save the downloaded file as:
echo C:\xampp\php\ext\php_xdebug.dll
echo.
pause

echo.
echo Step 3: Configuring php.ini...
powershell -ExecutionPolicy Bypass -File "%~dp0configure-xdebug.ps1"

echo.
echo Step 4: Verifying installation...
php -v

echo.
echo Installation complete!
echo Run: php artisan test --coverage
pause

