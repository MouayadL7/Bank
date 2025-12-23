@echo off
REM Bank Backend - Docker Quick Start Script for Windows
REM This script automates the Docker deployment process

echo ========================================
echo Bank Backend - Docker Deployment
echo ========================================
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not installed. Please install Docker Desktop first.
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker Compose is not installed. Please install Docker Desktop first.
    pause
    exit /b 1
)

echo [OK] Docker and Docker Compose are installed
echo.

REM Ask user for deployment mode
echo Select deployment mode:
echo 1) Standard (Nginx + PHP-FPM) - Recommended
echo 2) High Performance (Octane + Swoole)
echo 3) Development (All services + Tools)
echo.
set /p choice="Enter choice [1-3]: "

if "%choice%"=="1" (
    set MODE=standard
    echo [INFO] Deploying in Standard mode...
) else if "%choice%"=="2" (
    set MODE=octane
    echo [INFO] Deploying in High Performance (Octane) mode...
) else if "%choice%"=="3" (
    set MODE=development
    echo [INFO] Deploying in Development mode with all tools...
) else (
    echo [ERROR] Invalid choice. Exiting.
    pause
    exit /b 1
)

echo.
echo [INFO] Step 1: Stopping existing containers...
docker-compose down 2>nul
echo [OK] Existing containers stopped
echo.

echo [INFO] Step 2: Building Docker images...
docker-compose build --no-cache
if %errorlevel% neq 0 (
    echo [ERROR] Failed to build Docker images
    pause
    exit /b 1
)
echo [OK] Docker images built successfully
echo.

echo [INFO] Step 3: Starting services...
if "%MODE%"=="standard" (
    docker-compose up -d app mysql redis queue
) else if "%MODE%"=="octane" (
    docker-compose --profile octane up -d app-octane mysql redis queue
) else (
    docker-compose --profile tools --profile octane up -d
)

if %errorlevel% neq 0 (
    echo [ERROR] Failed to start services
    pause
    exit /b 1
)
echo [OK] Services started
echo.

echo [INFO] Step 4: Waiting for MySQL to be ready (30 seconds)...
timeout /t 30 /nobreak >nul
echo [OK] MySQL should be ready
echo.

echo [INFO] Step 5: Running database migrations...
if "%MODE%"=="octane" (
    docker-compose exec -T app-octane php artisan migrate --force
) else (
    docker-compose exec -T app php artisan migrate --force
)
echo [OK] Migrations completed
echo.

echo [INFO] Step 6: Seeding database...
if "%MODE%"=="octane" (
    docker-compose exec -T app-octane php artisan db:seed --force
) else (
    docker-compose exec -T app php artisan db:seed --force
)
echo [OK] Database seeded
echo.

echo [INFO] Step 7: Creating storage link...
if "%MODE%"=="octane" (
    docker-compose exec -T app-octane php artisan storage:link
) else (
    docker-compose exec -T app php artisan storage:link
)
echo [OK] Storage link created
echo.

echo [INFO] Step 8: Clearing caches...
if "%MODE%"=="octane" (
    docker-compose exec -T app-octane php artisan optimize:clear
) else (
    docker-compose exec -T app php artisan optimize:clear
)
echo [OK] Caches cleared
echo.

echo ========================================
echo [SUCCESS] Deployment completed!
echo ========================================
echo.

REM Show access URLs based on mode
if "%MODE%"=="standard" (
    echo Application URL: http://localhost:8000
    echo MySQL: localhost:3306
    echo Redis: localhost:6379
) else if "%MODE%"=="octane" (
    echo Application URL: http://localhost:8001 ^(Octane^)
    echo MySQL: localhost:3306
    echo Redis: localhost:6379
) else (
    echo Application URL ^(Standard^): http://localhost:8000
    echo Application URL ^(Octane^): http://localhost:8001
    echo MySQL: localhost:3306
    echo Redis: localhost:6379
    echo phpMyAdmin: http://localhost:8080
)

echo.
echo Useful commands:
echo   View logs:         docker-compose logs -f
echo   Stop services:     docker-compose down
echo   Restart:           docker-compose restart
echo   Access shell:      docker-compose exec app sh
echo.
echo [INFO] Check DOCKER_DEPLOYMENT.md for more details
echo.
pause

