@echo off
echo 🚀 Bắt đầu setup Laravel E-commerce Project...

REM Copy environment file
if not exist .env (
    echo 📄 Tạo file .env từ .env.example...
    copy .env.example .env
)

REM Start Docker containers
echo 🐳 Khởi động Docker containers...
docker-compose up -d

REM Wait for containers to be ready
echo ⏳ Đợi containers khởi động hoàn tất...
timeout /t 30 /nobreak > nul

REM Install Laravel if not exists
if not exist artisan (
    echo 📦 Cài đặt Laravel mới...
    docker-compose exec php composer create-project laravel/laravel . --prefer-dist
    echo ✅ Laravel đã được cài đặt!
)

REM Install Composer dependencies
echo 📦 Cài đặt dependencies...
docker-compose exec php composer install

REM Generate application key
echo 🔑 Tạo application key...
docker-compose exec php php artisan key:generate

REM Run migrations
echo 🗄️ Chạy migrations...
docker-compose exec php php artisan migrate

REM Create storage link
echo 🔗 Tạo storage link...
docker-compose exec php php artisan storage:link

REM Clear cache
echo 🧹 Xóa cache...
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan cache:clear

echo ✅ Setup cơ bản hoàn tất!
echo 🌐 Ứng dụng có thể truy cập tại: http://localhost
echo 📧 Mailhog có thể truy cập tại: http://localhost:8025
echo 🔧 Để vào PHP container: docker-compose exec php bash
echo.
echo 📦 Để cài đặt thêm packages:
echo   - JWT: docker-compose exec php composer require tymon/jwt-auth
echo   - Permissions: docker-compose exec php composer require spatie/laravel-permission
echo   - Image Processing: docker-compose exec php composer require intervention/image
echo.

pause
