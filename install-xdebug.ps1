# Xdebug Installation Script for PHP 8.2.12 ZTS
# Run this script as Administrator

Write-Host "Installing Xdebug for PHP 8.2.12 ZTS..." -ForegroundColor Green

$phpVersion = "8.2"
$phpBuild = "zts-vs16"
$arch = "x86_64"
$xdebugVersion = "3.3.2"
$extDir = "C:\xampp\php\ext"
$phpIni = "C:\xampp\php\php.ini"

# Download URL
$downloadUrl = "https://xdebug.org/files/php_xdebug-$xdebugVersion-$phpVersion-$phpBuild-$arch.dll"
$dllPath = "$extDir\php_xdebug.dll"

Write-Host "Downloading Xdebug from: $downloadUrl" -ForegroundColor Yellow

try {
    # Set TLS 1.2 for SSL
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    
    # Try to download using .NET WebClient
    $webClient = New-Object System.Net.WebClient
    $webClient.Headers.Add("User-Agent", "Mozilla/5.0")
    $webClient.DownloadFile($downloadUrl, $dllPath)
    Write-Host "Downloaded successfully!" -ForegroundColor Green
} catch {
    Write-Host "Download failed due to SSL/network issues." -ForegroundColor Red
    Write-Host ""
    Write-Host "Please download manually:" -ForegroundColor Yellow
    Write-Host "1. Open your browser and go to: https://xdebug.org/wizard" -ForegroundColor Cyan
    Write-Host "2. Copy and paste the output of: php -i" -ForegroundColor Cyan
    Write-Host "3. The wizard will provide the exact download link" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "OR download directly from:" -ForegroundColor Yellow
    Write-Host "URL: $downloadUrl" -ForegroundColor Cyan
    Write-Host "Save to: $dllPath" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "After downloading, run this script again to configure php.ini" -ForegroundColor Yellow
    
    # Still try to configure php.ini if DLL already exists
    if (Test-Path $dllPath) {
        Write-Host ""
        Write-Host "Xdebug DLL already exists. Configuring php.ini..." -ForegroundColor Yellow
    } else {
        exit 1
    }
}

# Check if file exists
if (Test-Path $dllPath) {
    Write-Host "Xdebug DLL found at: $dllPath" -ForegroundColor Green
    
    # Configure php.ini
    Write-Host "Configuring php.ini..." -ForegroundColor Yellow
    
    $phpIniContent = Get-Content $phpIni -Raw
    
    # Check if Xdebug is already configured
    if ($phpIniContent -match "zend_extension.*xdebug") {
        Write-Host "Xdebug is already configured in php.ini" -ForegroundColor Yellow
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
    Write-Host "Installation complete!" -ForegroundColor Green
    Write-Host "Please restart your web server or run: php -v" -ForegroundColor Yellow
    Write-Host "to verify Xdebug is loaded." -ForegroundColor Yellow
} else {
    Write-Host "Xdebug DLL not found. Please download manually." -ForegroundColor Red
    Write-Host "URL: $downloadUrl" -ForegroundColor Yellow
    Write-Host "Save to: $dllPath" -ForegroundColor Yellow
}

