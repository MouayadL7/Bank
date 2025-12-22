# Open Xdebug download page in browser
Write-Host "Opening Xdebug download page in your browser..." -ForegroundColor Green
Write-Host ""
Write-Host "Instructions:" -ForegroundColor Yellow
Write-Host "1. The browser will open to the Xdebug wizard" -ForegroundColor White
Write-Host "2. Copy the output below and paste it into the wizard" -ForegroundColor White
Write-Host "3. Download the DLL file to: C:\xampp\php\ext\php_xdebug.dll" -ForegroundColor White
Write-Host "4. Then run: powershell -ExecutionPolicy Bypass -File configure-xdebug.ps1" -ForegroundColor White
Write-Host ""

# Open browser to Xdebug wizard
Start-Process "https://xdebug.org/wizard"

# Also open direct download link
Start-Process "https://xdebug.org/files/php_xdebug-3.3.2-8.2-zts-vs16-x86_64.dll"

Write-Host ""
Write-Host "Your PHP info (copy this to the wizard):" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
php -i

