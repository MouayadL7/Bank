# ⚡ QUICK XDEBUG INSTALLATION - DO THIS NOW

## Step-by-Step (Takes 2 minutes)

### Step 1: Download Xdebug DLL
1. **Click this link** (it should open in your browser):
   https://xdebug.org/files/php_xdebug-3.3.2-8.2-zts-vs16-x86_64.dll

2. **Save the file** to this exact location:
   ```
   C:\xampp\php\ext\php_xdebug.dll
   ```
   ⚠️ **Important**: Make sure the filename is exactly `php_xdebug.dll` (not `php_xdebug-3.3.2-8.2-zts-vs16-x86_64.dll`)

### Step 2: Configure PHP
Run this command in PowerShell:
```powershell
cd D:\uni\SE3\Bank
powershell -ExecutionPolicy Bypass -File configure-xdebug.ps1
```

### Step 3: Verify
Run:
```powershell
php -v
```

You should see "with Xdebug" in the output.

### Step 4: Test Coverage
```powershell
php artisan test --coverage
```

---

## Alternative: If Download Link Doesn't Work

1. Go to: https://xdebug.org/wizard
2. Run: `php -i` and copy ALL the output
3. Paste it into the wizard
4. Download the DLL it provides
5. Save to: `C:\xampp\php\ext\php_xdebug.dll`
6. Run the configure script (Step 2 above)

---

## Troubleshooting

**If you see "zend_extension=xdebug" error:**
- Edit `C:\xampp\php\php.ini`
- Change `zend_extension=xdebug` to:
  ```ini
  zend_extension=C:\xampp\php\ext\php_xdebug.dll
  ```

**If Xdebug still doesn't load:**
- Make sure the DLL file exists in `C:\xampp\php\ext\`
- Check that it matches PHP 8.2.12 ZTS
- Restart your terminal/PowerShell
- If using Apache, restart it

