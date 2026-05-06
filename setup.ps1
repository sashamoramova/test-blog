# для запуска на Windows (PowerShell)

$ErrorActionPreference = "Stop"

function Step($msg) { Write-Host ""; Write-Host "==> $msg" -ForegroundColor Green }
function Warn($msg) { Write-Host "!! $msg"  -ForegroundColor Yellow }
function Fail($msg) { Write-Host "xx $msg"  -ForegroundColor Red; exit 1 }

Set-Location $PSScriptRoot

# 0. Prereqs
Step "Checking Docker..."
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) { Fail "Docker is not installed. Install Docker Desktop." }
docker compose version | Out-Null
if ($LASTEXITCODE -ne 0) { Fail "Docker Compose v2 is not available." }

# 1. .env
if (-not (Test-Path ".env")) {
    Step "Creating .env from .env.example"
    Copy-Item ".env.example" ".env"
} else {
    Warn ".env already exists, leaving it as is."
}

# 2. Up
Step "Starting containers (docker compose up -d --build)..."
docker compose up -d --build
if ($LASTEXITCODE -ne 0) { Fail "docker compose failed." }

# 3. Wait MySQL
Step "Waiting for MySQL..."
$ready = $false
for ($i = 1; $i -le 60; $i++) {
    docker compose exec -T mysql mysql -h127.0.0.1 -uroot -proot -e "SELECT 1" *> $null
    if ($LASTEXITCODE -eq 0) { $ready = $true; break }
    Write-Host -NoNewline "."
    Start-Sleep -Seconds 2
}
Write-Host ""
if (-not $ready) { Fail "MySQL did not become ready in 120 seconds. See: docker compose logs mysql" }

# 4. Migration
Step "Applying database migration..."
Get-Content -Raw migrations\001_init.sql |
    docker compose exec -T mysql mysql -h127.0.0.1 -uroot -proot blog
if ($LASTEXITCODE -ne 0) { Fail "Migration failed." }

# 5. Composer install
Step "Installing PHP dependencies..."
docker compose exec -T php composer install --no-interaction --quiet
if ($LASTEXITCODE -ne 0) { Fail "composer install failed." }

# 6. Seed
Step "Seeding test data (5 categories, 30 articles, 30 covers)..."
docker compose exec -T php php seeds/seed.php
if ($LASTEXITCODE -ne 0) { Fail "Seeding failed." }

# 7. Smoke check
Step "Smoke-checking the site..."
Start-Sleep -Seconds 2
try {
    $resp = Invoke-WebRequest -Uri "http://localhost:3000/" -UseBasicParsing -TimeoutSec 5
    if ($resp.StatusCode -eq 200) {
        Write-Host ""
        Write-Host "=========================================" -ForegroundColor Green
        Write-Host " Done! Open http://localhost:3000"        -ForegroundColor Green
        Write-Host "=========================================" -ForegroundColor Green
    } else {
        Warn "HTTP $($resp.StatusCode). Open http://localhost:3000 manually."
    }
} catch {
    Warn "HTTP check failed. Open http://localhost:3000 in the browser manually and check: docker compose logs"
}
