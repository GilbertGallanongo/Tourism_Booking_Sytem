# Local SQLite setup for Windows PowerShell
Set-StrictMode -Version Latest
$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectRoot

Write-Host '== Local setup script: configure SQLite and run migrations =='

if (-Not (Test-Path .env)) {
    if (Test-Path .env.example) {
        Copy-Item .env.example .env
        Write-Host 'Copied .env.example to .env'
    } else {
        Write-Error 'No .env or .env.example found. Create .env manually.'
        exit 1
    }
}

# Replace or add values in .env
function Set-EnvValue {
    param(
        [string]$Key,
        [string]$Value
    )
    $content = Get-Content .env -Raw
    if ($content -match "(?m)^$Key=") {
        $content = [regex]::Replace($content, "(?m)^$Key=.*", "$Key=$Value")
    } else {
        $content += "`n$Key=$Value"
    }
    Set-Content .env $content
}

Set-EnvValue 'APP_ENV' 'local'
Set-EnvValue 'APP_DEBUG' 'true'
Set-EnvValue 'DB_CONNECTION' 'sqlite'
Set-EnvValue 'DB_DATABASE' "$projectRoot\database\database.sqlite"
Set-EnvValue 'SESSION_DRIVER' 'file'

if (-Not (Test-Path database)) {
    New-Item -ItemType Directory -Path database | Out-Null
}
if (-Not (Test-Path database\database.sqlite)) {
    New-Item -ItemType File -Path database\database.sqlite | Out-Null
    Write-Host 'Created database\database.sqlite'
}

if (-Not (Test-Path vendor)) {
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Write-Host 'Installing PHP dependencies via composer...'
        composer install --no-interaction --prefer-dist
    } else {
        Write-Warning 'composer not found. Please run composer install manually.'
    }
}

php artisan config:clear | Out-Null
php artisan migrate --force

Write-Host ''
Write-Host 'Setup complete.'
Write-Host 'Run the app with:'
Write-Host '  php artisan serve --host=127.0.0.1 --port=8000'
Write-Host 'Then verify diagnostics:'
Write-Host '  curl -sS http://127.0.0.1:8000/_diagnostics | jq .'
Write-Host '  tail -n 200 storage/logs/laravel.log'
