# ==============================================================================
# Smart Car Parking Management System - Quick Stop Script
# ==============================================================================

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  Stopping Smart Car Parking System (Docker + Kubernetes) " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Stop Docker Compose
Write-Host "`n[1/2] Stopping Docker Compose containers..." -ForegroundColor Yellow
Set-Location "C:\S5 Project"
docker compose stop
Write-Host "✔ Docker Compose containers stopped." -ForegroundColor Green

# 2. Stop Kind control plane container
Write-Host "`n[2/2] Stopping Kubernetes cluster container..." -ForegroundColor Yellow
docker stop parking-cluster-control-plane
Write-Host "✔ Kubernetes cluster stopped." -ForegroundColor Green

Write-Host "`nAll services have been safely stopped. Run .\start-all.ps1 to resume." -ForegroundColor Cyan
