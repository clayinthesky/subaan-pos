$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
if ($phpCommand) { $phpPath = $phpCommand.Source } elseif (Test-Path 'C:\xampp\php\php.exe') { $phpPath = 'C:\xampp\php\php.exe' } else { throw 'Install PHP 8.2 or newer and add it to PATH.' }
$flags = @()
$modules = & $phpPath -m
foreach ($module in @('intl','gd','sqlite3')) { if ($modules -notcontains $module) { $flags += @('-d', "extension=$module") } }
if (!(Test-Path '.env')) { Copy-Item '.env.example' '.env' }
New-Item -ItemType Directory -Force 'writable/tmp' | Out-Null
$flags += @('-d', ('upload_tmp_dir=' + (Join-Path $PSScriptRoot 'writable/tmp')))
& $phpPath @flags spark migrate
if ($LASTEXITCODE -ne 0) { throw 'Migration failed.' }
& $phpPath @flags -S localhost:8080 -t public vendor/codeigniter4/framework/system/rewrite.php
