Param(
    [string]$FrontendPath = "..\mi-amor-lura-frontend",
    [string]$FrontendStartCommand = "npm start",
    [string]$BackendHost = "127.0.0.1",
    [int]$BackendPort = 8000
)

Write-Host "Iniciando backend (Laravel)..."
$backendCmd = "cd `"$PSScriptRoot`"; php artisan serve --host=$BackendHost --port=$BackendPort"
Start-Process powershell -ArgumentList "-NoExit","-Command","$backendCmd"

if (Test-Path $FrontendPath) {
    Write-Host "Iniciando frontend en: $FrontendPath"
    $frontendCmd = "cd `"$(Resolve-Path $FrontendPath)`"; $FrontendStartCommand"
    Start-Process powershell -ArgumentList "-NoExit","-Command","$frontendCmd"
} else {
    Write-Host "Ruta de frontend no encontrada: $FrontendPath"
    Write-Host "Abre otra ventana y ejecuta manualmente el frontend (ej. cd path/to/frontend && npm start)"
}

Write-Host "Script ejecutado. Revisa las ventanas nuevas de PowerShell para ver la salida."