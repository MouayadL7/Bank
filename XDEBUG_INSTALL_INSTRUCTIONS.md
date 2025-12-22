# Xdebug Installation Instructions

## Step 1: Download Xdebug DLL

Due to SSL/network restrictions, please download Xdebug manually:

### Option A: Using Xdebug Wizard (Recommended)
1. Open your browser and go to: **https://xdebug.org/wizard**
2. Run this command in PowerShell and copy ALL the output:
   ```powershell
   php -i
   ```
3. Paste the output into the Xdebug wizard
4. The wizard will provide the exact download link for your PHP version
5. Download the DLL file

### Option B: Direct Download
For PHP 8.2.12 ZTS (Thread Safe) on Windows x64:
- **URL**: https://xdebug.org/files/php_xdebug-3.3.2-8.2-zts-vs16-x86_64.dll
- **Save as**: `C:\xampp\php\ext\php_xdebug.dll`

## Step 2: Configure php.ini

After downloading the DLL, run the configuration script:

```powershell
cd D:\uni\SE3\Bank
powershell -ExecutionPolicy Bypass -File configure-xdebug.ps1
```

Or manually add these lines to `C:\xampp\php\php.ini`:

```ini
[Xdebug]
zend_extension=xdebug
xdebug.mode=coverage
xdebug.start_with_request=yes
```

## Step 3: Verify Installation

Run this command to verify Xdebug is loaded:

```powershell
php -v
```

You should see "with Xdebug" in the output.

## Step 4: Test Code Coverage

Run your tests with coverage:

```powershell
php artisan test --coverage
```

## Troubleshooting

- If you see "zend_extension=xdebug" error, change it to the full path:
  ```ini
  zend_extension=C:\xampp\php\ext\php_xdebug.dll
  ```

- If Xdebug still doesn't load, check:
  - The DLL file exists in `C:\xampp\php\ext\`
  - The DLL matches your PHP version (8.2.12) and build (ZTS)
  - No syntax errors in php.ini
  - Restart your web server if using Apache

