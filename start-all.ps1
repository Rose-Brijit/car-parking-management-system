# ==============================================================================
# Smart Car Parking Management System - Quick Start / Restart Script
# ==============================================================================

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  Starting Smart Car Parking System (Docker + Kubernetes) " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Ensure Docker Desktop is running
Write-Host "`n[1/4] Checking Docker status..." -ForegroundColor Yellow
$dockerReady = $false
try {
    $null = docker info 2>$null
    if ($LASTEXITCODE -eq 0) { $dockerReady = $true }
} catch {}

if (-not $dockerReady) {
    Write-Host "Docker is not running. Starting Docker Desktop..." -ForegroundColor Yellow
    Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe"
    Write-Host "Waiting for Docker daemon to initialize (this may take 20-30 seconds)..." -ForegroundColor Yellow
    $retries = 30
    while ($retries -gt 0 -and -not $dockerReady) {
        Start-Sleep -Seconds 2
        try {
            $null = docker info 2>$null
            if ($LASTEXITCODE -eq 0) { $dockerReady = $true; break }
        } catch {}
        $retries--
    }
}

if (-not $dockerReady) {
    Write-Host "❌ Could not connect to Docker. Please ensure Docker Desktop is open and running." -ForegroundColor Red
    Exit 1
}
Write-Host "✔ Docker daemon is active." -ForegroundColor Green

# 2. Start Docker Compose services
Write-Host "`n[2/4] Starting Docker Compose application (Port 8080)..." -ForegroundColor Yellow
Set-Location "C:\S5 Project"
docker compose up -d
Write-Host "✔ Docker Compose containers started." -ForegroundColor Green

# 3. Ensure Kubernetes (Kind) cluster is running
Write-Host "`n[3/4] Checking Kubernetes (Kind) cluster..." -ForegroundColor Yellow
$kindRunning = docker ps --filter "name=parking-cluster-control-plane" --format "{{.Status}}"
if ($kindRunning -notmatch "Up") {
    Write-Host "Starting Kind cluster container..." -ForegroundColor Yellow
    docker start parking-cluster-control-plane
    Start-Sleep -Seconds 5
}

# Verify pods in Kubernetes
Write-Host "Checking Kubernetes pods in 'car-parking' namespace..." -ForegroundColor Yellow
kubectl get pods -n car-parking

# 4. Display URLs
Write-Host "`n==========================================================" -ForegroundColor Cyan
Write-Host "  All Services are Active!                                " -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "🌐 Kubernetes Web App : http://localhost:30080" -ForegroundColor White
Write-Host "🐳 Docker Compose App : http://localhost:8080" -ForegroundColor White
Write-Host "🔑 Admin Credentials  : admin / admin123" -ForegroundColor White
Write-Host "🔑 User Credentials   : aami  / 1234" -ForegroundColor White
Write-Host "==========================================================`n" -ForegroundColor Cyan
