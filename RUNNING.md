Instrucciones para ejecutar el proyecto (Frontend + Backend)

Resumen
- Backend: Laravel (esta carpeta `mi-amor-lura-backend`).
- Frontend: proyecto separado (ej. `mi-amor-lura-frontend`) en otra carpeta/VS Code.

Pasos (Backend)
1. Instalar dependencias PHP:

```powershell
composer install
```

2. Copiar el archivo de entorno y generar la clave:

```powershell
copy .env.example .env
php artisan key:generate
```

3. Configura la base de datos en `.env` y ejecuta migraciones si aplica:

```powershell
php artisan migrate
```

4. Instalar y compilar assets (si usas Vite):

```powershell
npm install
npm run dev
```

Pasos (Frontend)
- Ve a la carpeta del frontend (por ejemplo `..\mi-amor-lura-frontend`) y ejecuta:

```powershell
npm install
npm start
```

Levantar ambos proyectos juntos (Windows PowerShell)
- He añadido un script PowerShell en `scripts/start-all.ps1` que intenta abrir dos ventanas de PowerShell: una para el backend (php artisan serve) y otra para el frontend (ejecuta `npm start` en la ruta configurada).

Ejecuta desde la raíz del backend:

```powershell
.\scripts\start-all.ps1 -FrontendPath "..\mi-amor-lura-frontend" -FrontendStartCommand "npm start"
```

Ajusta `-FrontendPath` si la carpeta del frontend está en otra ubicación.

URLs recomendadas
- Backend (API): http://127.0.0.1:8000
- Frontend (Angular/Ionic por defecto): http://localhost:4200
- Vite (si aplica para assets): http://localhost:5173

CORS y conexión entre front y back
- Abre `config/cors.php` y asegúrate de permitir el origen del frontend, por ejemplo:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:4200'],
```

- En el backend `.env` define `APP_URL=http://127.0.0.1:8000` y cualquier variable de API que use el frontend (p. ej. `API_URL`). En el frontend, apunta las llamadas API a `http://127.0.0.1:8000/api` según tu configuración de rutas.

Notas finales
- Si el frontend está en otro equipo o puerto distinto, actualiza `allowed_origins` en `config/cors.php` y la variable `FrontendPath` al ejecutar el script.
- Si prefieres, ejecútalo todo manualmente en dos terminales: uno con `php artisan serve` y otro en la carpeta del frontend con `npm start`.

Si quieres, puedo también:
- Añadir estas instrucciones al `README.md` principal.
- Configurar un `composer` o `npm` script que abra ambos servicios.
- Ejecutar comandos aquí (si me autorizas) para instalar dependencias o ejecutar el script.
