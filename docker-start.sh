#!/bin/bash

# Bank Backend - Docker Quick Start Script
# This script automates the Docker deployment process

set -e

echo "🏦 Bank Backend - Docker Deployment"
echo "===================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_success "Docker and Docker Compose are installed"

# Ask user for deployment mode
echo ""
echo "Select deployment mode:"
echo "1) Standard (Nginx + PHP-FPM) - Recommended"
echo "2) High Performance (Octane + Swoole)"
echo "3) Development (All services + Tools)"
read -p "Enter choice [1-3]: " choice

case $choice in
    1)
        MODE="standard"
        print_info "Deploying in Standard mode..."
        ;;
    2)
        MODE="octane"
        print_info "Deploying in High Performance (Octane) mode..."
        ;;
    3)
        MODE="development"
        print_info "Deploying in Development mode with all tools..."
        ;;
    *)
        print_error "Invalid choice. Exiting."
        exit 1
        ;;
esac

echo ""
print_info "Step 1: Stopping existing containers..."
docker-compose down 2>/dev/null || true
print_success "Existing containers stopped"

echo ""
print_info "Step 2: Building Docker images..."
docker-compose build --no-cache
print_success "Docker images built successfully"

echo ""
print_info "Step 3: Starting services..."

if [ "$MODE" == "standard" ]; then
    docker-compose up -d app mysql redis queue
elif [ "$MODE" == "octane" ]; then
    docker-compose --profile octane up -d app-octane mysql redis queue
else
    docker-compose --profile tools --profile octane up -d
fi

print_success "Services started"

echo ""
print_info "Step 4: Waiting for MySQL to be ready (30 seconds)..."
sleep 30
print_success "MySQL should be ready"

echo ""
print_info "Step 5: Running database migrations..."
if [ "$MODE" == "octane" ]; then
    docker-compose exec -T app-octane php artisan migrate --force
else
    docker-compose exec -T app php artisan migrate --force
fi
print_success "Migrations completed"

echo ""
print_info "Step 6: Seeding database..."
if [ "$MODE" == "octane" ]; then
    docker-compose exec -T app-octane php artisan db:seed --force
else
    docker-compose exec -T app php artisan db:seed --force
fi
print_success "Database seeded"

echo ""
print_info "Step 7: Creating storage link..."
if [ "$MODE" == "octane" ]; then
    docker-compose exec -T app-octane php artisan storage:link
else
    docker-compose exec -T app php artisan storage:link
fi
print_success "Storage link created"

echo ""
print_info "Step 8: Clearing caches..."
if [ "$MODE" == "octane" ]; then
    docker-compose exec -T app-octane php artisan optimize:clear
else
    docker-compose exec -T app php artisan optimize:clear
fi
print_success "Caches cleared"

echo ""
echo "===================================="
print_success "Deployment completed successfully!"
echo "===================================="
echo ""

# Show access URLs based on mode
if [ "$MODE" == "standard" ]; then
    echo "📱 Application URL: http://localhost:8000"
    echo "🗄️  MySQL: localhost:3306"
    echo "💾 Redis: localhost:6379"
elif [ "$MODE" == "octane" ]; then
    echo "📱 Application URL: http://localhost:8001 (Octane)"
    echo "🗄️  MySQL: localhost:3306"
    echo "💾 Redis: localhost:6379"
else
    echo "📱 Application URL (Standard): http://localhost:8000"
    echo "📱 Application URL (Octane): http://localhost:8001"
    echo "🗄️  MySQL: localhost:3306"
    echo "💾 Redis: localhost:6379"
    echo "🔧 phpMyAdmin: http://localhost:8080"
fi

echo ""
echo "Useful commands:"
echo "  View logs:         docker-compose logs -f"
echo "  Stop services:     docker-compose down"
echo "  Restart:           docker-compose restart"
echo "  Access shell:      docker-compose exec app sh"
echo ""
print_info "Check DOCKER_DEPLOYMENT.md for more details"

