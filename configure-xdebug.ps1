# Configure Xdebug in php.ini
# Run this AFTER manually downloading php_xdebug.dll to C:\xampp\php\ext\

$phpIni = "C:\xampp\php\php.ini"
$dllPath = "C:\xampp\php\ext\php_xdebug.dll"

Write-Host "Configuring Xdebug in php.ini..." -ForegroundColor Yellow

if (-not (Test-Path $dllPath)) {
    Write-Host "ERROR: php_xdebug.dll not found at: $dllPath" -ForegroundColor Red
    Write-Host "Please download it first from: https://xdebug.org/wizard" -ForegroundColor Yellow
    exit 1
}

$phpIniContent = Get-Content $phpIni -Raw

# Check if Xdebug is already configured
if ($phpIniContent -match "zend_extension.*xdebug") {
    Write-Host "Xdebug is already configured in php.ini" -ForegroundColor Yellow
    Write-Host "Current configuration:" -ForegroundColor Cyan
    Select-String -Path $phpIni -Pattern "xdebug" -Context 0,2
} else {
    # Add Xdebug configuration at the end
    $xdebugConfig = @"

[Xdebug]
zend_extension=xdebug
xdebug.mode=coverage
xdebug.start_with_request=yes
"@
    
    Add-Content -Path $phpIni -Value $xdebugConfig
    Write-Host "Xdebug configuration added to php.ini" -ForegroundColor Green
}

Write-Host ""
Write-Host "Configuration complete!" -ForegroundColor Green
Write-Host "Run 'php -v' to verify Xdebug is loaded." -ForegroundColor Yellow

