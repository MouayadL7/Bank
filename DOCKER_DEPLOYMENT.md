# Docker Deployment Guide - Bank Backend

This guide explains how to deploy the Laravel backend using Docker.

## 📋 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- 2GB RAM minimum (4GB recommended)
- 10GB free disk space

## 🚀 Quick Start

### 1. Build and Start Services

```bash
# Navigate to project directory
cd /path/to/Bank

# Start services (with standard PHP-FPM + Nginx)
docker-compose up -d app mysql redis queue

# OR start with Octane (high performance)
docker-compose --profile octane up -d app-octane mysql redis queue
```

### 2. Run Initial Setup

```bash
# Wait for MySQL to be ready (30 seconds)
sleep 30

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed database
docker-compose exec app php artisan db:seed --force

# Create storage link
docker-compose exec app php artisan storage:link
```

### 3. Access the Application

- **Standard Mode**: http://localhost:8000
- **Octane Mode**: http://localhost:8001
- **phpMyAdmin** (optional): http://localhost:8080

---

## 🎯 Deployment Options

### Option 1: Standard Production (Nginx + PHP-FPM)
```bash
docker-compose up -d app mysql redis queue
```
- Uses Nginx as web server
- PHP-FPM for processing
- Suitable for most deployments
- Port: 8000

### Option 2: High Performance (Octane + Swoole)
```bash
docker-compose --profile octane up -d app-octane mysql redis queue
```
- Uses Laravel Octane with Swoole
- Better performance and concurrency
- Ideal for high-traffic applications
- Port: 8001

### Option 3: Development with Tools
```bash
docker-compose --profile tools --profile octane up -d
```
- Includes phpMyAdmin for database management
- All services enabled

---

## 📦 Docker Services

| Service | Description | Port | Container Name |
|---------|-------------|------|----------------|
| **app** | Laravel with Nginx + PHP-FPM | 8000 | bank_app |
| **app-octane** | Laravel Octane (Swoole) | 8001 | bank_app_octane |
| **mysql** | MySQL 8.0 Database | 3306 | bank_mysql |
| **redis** | Redis Cache & Queue | 6379 | bank_redis |
| **queue** | Laravel Queue Worker | - | bank_queue |
| **phpmyadmin** | Database Management (optional) | 8080 | bank_phpmyadmin |

---

## 🔧 Configuration

### Environment Variables

Create `.env` file or edit `docker-compose.yml` environment section:

```env
# Application
APP_NAME=BankPortal
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=bank_db
DB_USERNAME=bank_user
DB_PASSWORD=bank_password

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379

# Mail (configure as needed)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### MySQL Configuration

Default credentials (change in production):
- Root Password: `root_password`
- Database: `bank_db`
- User: `bank_user`
- Password: `bank_password`

---

## 🛠️ Common Commands

### Service Management

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart service
docker-compose restart app

# View logs
docker-compose logs -f app

# View all logs
docker-compose logs -f
```

### Laravel Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Generate API documentation
docker-compose exec app php artisan l5-swagger:generate

# Run queue worker manually
docker-compose exec app php artisan queue:work

# Access Laravel Tinker
docker-compose exec app php artisan tinker
```

### Database Commands

```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u bank_user -pbank_password bank_db

# Backup database
docker-compose exec mysql mysqldump -u root -proot_password bank_db > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u root -proot_password bank_db < backup.sql

# Access Redis CLI
docker-compose exec redis redis-cli
```

### Container Management

```bash
# List running containers
docker-compose ps

# Access container shell
docker-compose exec app sh

# View container logs
docker-compose logs app

# Check container resource usage
docker stats
```

---

## 🏗️ Build Options

### Building Images

```bash
# Build production image
docker build --target production -t bank-backend:latest .

# Build Octane image
docker build --target octane -t bank-backend:octane .

# Build development image
docker build --target development -t bank-backend:dev .

# Build with no cache
docker-compose build --no-cache
```

### Multi-Stage Builds

The Dockerfile includes 4 stages:
1. **base** - Base dependencies
2. **development** - Development dependencies
3. **production** - Optimized for production with Nginx
4. **octane** - High-performance with Swoole

---

## 🔒 Security Best Practices

### For Production Deployment:

1. **Change Default Passwords**
```bash
# Update in docker-compose.yml
MYSQL_ROOT_PASSWORD: your_secure_password
DB_PASSWORD: your_secure_password
```

2. **Set APP_KEY**
```bash
docker-compose exec app php artisan key:generate
```

3. **Enable HTTPS**
   - Use reverse proxy (Nginx/Traefik) with SSL
   - Update APP_URL to https://

4. **Restrict Port Access**
   - Only expose necessary ports (80/443)
   - Use firewall rules

5. **Regular Updates**
```bash
docker-compose pull
docker-compose up -d
```

---

## 📊 Monitoring

### Health Checks

```bash
# Check application health
curl http://localhost:8000/health

# Check PHP-FPM status
curl http://localhost:8000/status

# Check database connection
docker-compose exec app php artisan db:show
```

### Performance Monitoring

```bash
# View container stats
docker stats

# Monitor logs
docker-compose logs -f --tail=100

# Check queue status
docker-compose exec app php artisan queue:failed
```

---

## 🐛 Troubleshooting

### Container Won't Start

```bash
# Check logs
docker-compose logs app

# Check port conflicts
netstat -tulpn | grep 8000

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### Database Connection Issues

```bash
# Wait for MySQL to be ready
docker-compose exec mysql mysqladmin ping -h localhost

# Check MySQL logs
docker-compose logs mysql

# Test connection
docker-compose exec app php artisan db:show
```

### Cache Issues

```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Rebuild caches
docker-compose exec app php artisan optimize
```

---

## 🔄 Updating the Application

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose build

# Restart services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear and rebuild caches
docker-compose exec app php artisan optimize
```

---

## 💾 Backup & Restore

### Backup

```bash
# Backup database
docker-compose exec mysql mysqldump -u root -proot_password bank_db > backup-$(date +%Y%m%d).sql

# Backup volumes
docker run --rm -v bank_mysql_data:/data -v $(pwd):/backup alpine tar czf /backup/mysql-data-$(date +%Y%m%d).tar.gz -C /data .
```

### Restore

```bash
# Restore database
docker-compose exec -T mysql mysql -u root -proot_password bank_db < backup.sql

# Restore volumes
docker run --rm -v bank_mysql_data:/data -v $(pwd):/backup alpine tar xzf /backup/mysql-data.tar.gz -C /data
```

---

## 📈 Scaling

### Scale Queue Workers

```bash
# Scale to 3 queue workers
docker-compose up -d --scale queue=3

# View scaled workers
docker-compose ps queue
```

### Load Balancing

For production with load balancing:
1. Use Docker Swarm or Kubernetes
2. Add reverse proxy (Nginx/HAProxy)
3. Configure session sharing (Redis)

---

## 🎯 Production Checklist

- [ ] Change all default passwords
- [ ] Set APP_KEY
- [ ] Configure database credentials
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Configure mail settings
- [ ] Set up HTTPS/SSL
- [ ] Configure backup strategy
- [ ] Set up monitoring
- [ ] Configure log rotation
- [ ] Test rollback procedure
- [ ] Document deployment process
- [ ] Set up health checks
- [ ] Configure firewall rules

---

## 📞 Support

For issues or questions:
1. Check logs: `docker-compose logs -f`
2. Review this guide
3. Check Laravel logs in `storage/logs/`
4. Contact development team

---

**Deployment Date**: December 2025  
**Docker Version**: 24.0+  
**Docker Compose Version**: 2.0+

