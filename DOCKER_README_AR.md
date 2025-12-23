# 🐳 دليل Docker للـ Backend - نظام البنك

## 📋 نظرة عامة

تم إنشاء Docker setup متكامل لمشروع الـ Laravel Backend مع جميع الخدمات المطلوبة.

## 📦 الملفات المُنشأة

### 1. الملفات الرئيسية
- ✅ `Dockerfile` - ملف Docker الرئيسي (4 مراحل بناء)
- ✅ `docker-compose.yml` - تنسيق الخدمات
- ✅ `.dockerignore` - الملفات المُستبعدة من الـ image

### 2. ملفات التكوين
- ✅ `docker/nginx/nginx.conf` - إعدادات Nginx الرئيسية
- ✅ `docker/nginx/default.conf` - إعدادات موقع Laravel
- ✅ `docker/php/php.ini` - إعدادات PHP
- ✅ `docker/php/php-fpm.conf` - إعدادات PHP-FPM
- ✅ `docker/supervisor/supervisord.conf` - إدارة العمليات
- ✅ `docker/mysql/my.cnf` - إعدادات MySQL

### 3. سكريبتات التشغيل
- ✅ `docker-start.sh` - تشغيل تلقائي (Linux/Mac)
- ✅ `docker-start.bat` - تشغيل تلقائي (Windows)

### 4. الوثائق
- ✅ `DOCKER_DEPLOYMENT.md` - دليل شامل بالإنجليزية
- ✅ `DOCKER_README_AR.md` - هذا الملف

---

## 🚀 طرق التشغيل

### الطريقة 1: تشغيل تلقائي (الأسهل) 🌟

#### على Windows:
```bash
# شغل السكريبت مباشرة
docker-start.bat
```

#### على Linux/Mac:
```bash
# اعطي صلاحيات التنفيذ
chmod +x docker-start.sh

# شغل السكريبت
./docker-start.sh
```

**السكريبت يقوم بـ:**
1. ✅ فحص تثبيت Docker
2. ✅ بناء الـ images
3. ✅ تشغيل الخدمات
4. ✅ انتظار MySQL
5. ✅ تنفيذ migrations
6. ✅ ملء قاعدة البيانات
7. ✅ إنشاء روابط التخزين
8. ✅ مسح الـ cache

### الطريقة 2: تشغيل يدوي

#### أ) الوضع القياسي (Nginx + PHP-FPM)
```bash
# تشغيل الخدمات
docker-compose up -d app mysql redis queue

# انتظر 30 ثانية لـ MySQL
sleep 30

# تنفيذ migrations
docker-compose exec app php artisan migrate --force

# ملء البيانات
docker-compose exec app php artisan db:seed --force
```

#### ب) الوضع عالي الأداء (Octane + Swoole)
```bash
# تشغيل الخدمات
docker-compose --profile octane up -d app-octane mysql redis queue

# انتظر 30 ثانية
sleep 30

# تنفيذ migrations
docker-compose exec app-octane php artisan migrate --force

# ملء البيانات
docker-compose exec app-octane php artisan db:seed --force
```

---

## 🎯 الخدمات المتاحة

| الخدمة | الوصف | المنفذ | اسم الـ Container |
|-------|-------|-------|------------------|
| **app** | Laravel مع Nginx + PHP-FPM | 8000 | bank_app |
| **app-octane** | Laravel Octane (Swoole) | 8001 | bank_app_octane |
| **mysql** | قاعدة بيانات MySQL 8.0 | 3306 | bank_mysql |
| **redis** | Redis للـ Cache و Queue | 6379 | bank_redis |
| **queue** | معالج طوابير Laravel | - | bank_queue |
| **phpmyadmin** | إدارة قاعدة البيانات (اختياري) | 8080 | bank_phpmyadmin |

---

## 🌐 الروابط بعد التشغيل

### الوضع القياسي:
- التطبيق: http://localhost:8000
- قاعدة البيانات: localhost:3306
- Redis: localhost:6379

### وضع Octane:
- التطبيق: http://localhost:8001
- قاعدة البيانات: localhost:3306
- Redis: localhost:6379

### مع الأدوات (optional):
- phpMyAdmin: http://localhost:8080

---

## 🔧 أوامر مفيدة

### إدارة الخدمات
```bash
# عرض الخدمات العاملة
docker-compose ps

# عرض السجلات
docker-compose logs -f app

# إيقاف الخدمات
docker-compose down

# إعادة تشغيل
docker-compose restart app

# الدخول إلى shell الـ container
docker-compose exec app sh
```

### أوامر Laravel
```bash
# تنفيذ migrations
docker-compose exec app php artisan migrate

# مسح الـ cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# توليد مفتاح التطبيق
docker-compose exec app php artisan key:generate

# الوصول إلى Tinker
docker-compose exec app php artisan tinker

# عرض الجداول
docker-compose exec app php artisan db:show
```

### أوامر قاعدة البيانات
```bash
# الدخول إلى MySQL
docker-compose exec mysql mysql -u bank_user -pbank_password bank_db

# النسخ الاحتياطي
docker-compose exec mysql mysqldump -u root -proot_password bank_db > backup.sql

# الاستعادة
docker-compose exec -T mysql mysql -u root -proot_password bank_db < backup.sql

# الدخول إلى Redis
docker-compose exec redis redis-cli
```

---

## 🔐 معلومات قاعدة البيانات الافتراضية

⚠️ **مهم:** غيّر هذه البيانات في بيئة الإنتاج!

```
Database Name: bank_db
Username: bank_user
Password: bank_password
Root Password: root_password
```

---

## 🏗️ هيكل Dockerfile

تم بناء الـ Dockerfile بـ 4 مراحل:

### 1. base
- تثبيت PHP 8.2 و Extensions
- تثبيت Composer
- التبعيات الأساسية

### 2. development
- جميع التبعيات بما فيها dev
- مناسب للتطوير

### 3. production
- تبعيات الإنتاج فقط
- Nginx + PHP-FPM
- محسّن للأداء
- Port: 80

### 4. octane
- Laravel Octane مع Swoole
- أداء عالي جداً
- مناسب للتطبيقات كثيرة الاستخدام
- Port: 8000

---

## 📊 مقارنة الأوضاع

| الميزة | القياسي | Octane |
|-------|---------|--------|
| الأداء | جيد | ممتاز |
| استهلاك الذاكرة | متوسط | أعلى قليلاً |
| التوافق | عالي | جيد |
| سهولة الإعداد | بسيط | بسيط |
| الاستخدام المثالي | معظم التطبيقات | تطبيقات عالية الحمل |

---

## 🐛 حل المشاكل

### المشكلة: Container لا يعمل
```bash
# عرض السجلات
docker-compose logs app

# إعادة البناء
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### المشكلة: صلاحيات الملفات
```bash
# إصلاح صلاحيات storage
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### المشكلة: خطأ في الاتصال بقاعدة البيانات
```bash
# انتظر حتى يكون MySQL جاهز
docker-compose exec mysql mysqladmin ping -h localhost

# اختبار الاتصال
docker-compose exec app php artisan db:show
```

### المشكلة: Port مستخدم
```bash
# Windows
netstat -ano | findstr :8000

# Linux/Mac
lsof -i :8000

# غيّر الـ Port في docker-compose.yml
# من "8000:80" إلى "8001:80" مثلاً
```

---

## 🔄 التحديث

```bash
# سحب أحدث التغييرات
git pull origin main

# إعادة بناء الـ images
docker-compose build

# إعادة تشغيل الخدمات
docker-compose up -d

# تنفيذ migrations الجديدة
docker-compose exec app php artisan migrate --force

# مسح الـ cache
docker-compose exec app php artisan optimize
```

---

## 💾 النسخ الاحتياطي

### نسخ احتياطي لقاعدة البيانات
```bash
# Windows
docker-compose exec mysql mysqldump -u root -proot_password bank_db > backup-%date:~10,4%%date:~4,2%%date:~7,2%.sql

# Linux/Mac
docker-compose exec mysql mysqldump -u root -proot_password bank_db > backup-$(date +%Y%m%d).sql
```

### استعادة قاعدة البيانات
```bash
docker-compose exec -T mysql mysql -u root -proot_password bank_db < backup.sql
```

---

## 📈 تحسين الأداء

### 1. زيادة عدد Queue Workers
```bash
docker-compose up -d --scale queue=3
```

### 2. استخدام Octane للأداء العالي
```bash
docker-compose --profile octane up -d app-octane mysql redis queue
```

### 3. تحسين MySQL
- زيادة `innodb_buffer_pool_size` في `docker/mysql/my.cnf`
- تعديل `max_connections` حسب الحاجة

### 4. تحسين PHP-FPM
- تعديل `pm.max_children` في `docker/php/php-fpm.conf`
- زيادة `memory_limit` في `docker/php/php.ini`

---

## ✅ قائمة التحقق للإنتاج

قبل النشر في بيئة الإنتاج:

- [ ] تغيير جميع كلمات المرور الافتراضية
- [ ] تعيين `APP_KEY`
- [ ] ضبط `APP_DEBUG=false`
- [ ] ضبط `APP_ENV=production`
- [ ] تكوين البريد الإلكتروني
- [ ] إعداد HTTPS/SSL
- [ ] تكوين النسخ الاحتياطي
- [ ] إعداد المراقبة
- [ ] تكوين Firewall
- [ ] اختبار عملية الاستعادة
- [ ] توثيق عملية النشر

---

## 📚 مصادر إضافية

- **الدليل الشامل (إنجليزي):** `DOCKER_DEPLOYMENT.md`
- **وثائق Laravel:** https://laravel.com/docs
- **وثائق Docker:** https://docs.docker.com
- **وثائق Laravel Octane:** https://laravel.com/docs/octane

---

## 🆘 الدعم

إذا واجهت أي مشاكل:

1. ✅ تحقق من السجلات: `docker-compose logs -f`
2. ✅ راجع هذا الدليل
3. ✅ تحقق من سجلات Laravel في `storage/logs/`
4. ✅ اتصل بفريق التطوير

---

## 🎯 ملخص سريع

### للتشغيل السريع:
```bash
# Windows
docker-start.bat

# Linux/Mac
./docker-start.sh
```

### الوصول للتطبيق:
- **Standard:** http://localhost:8000
- **Octane:** http://localhost:8001

### لإيقاف الخدمات:
```bash
docker-compose down
```

---

**تم الإنشاء:** ديسمبر 2025  
**الإصدار:** 1.0  
**نظام التشغيل:** Windows / Linux / macOS  
**Docker:** 20.10+ مطلوب  
**Docker Compose:** 2.0+ مطلوب

---

🎉 **كل شيء جاهز! شغّل المشروع الآن** 🎉

