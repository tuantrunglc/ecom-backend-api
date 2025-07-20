#!/bin/bash

echo "🚀 Bắt đầu setup Laravel E-commerce Project..."

# Copy environment file
if [ ! -f .env ]; then
    echo "📄 Tạo file .env từ .env.example..."
    cp .env.example .env
fi

# Start Docker containers
echo "🐳 Khởi động Docker containers..."
docker-compose up -d

# Wait for containers to be ready
echo "⏳ Đợi containers khởi động hoàn tất..."
sleep 30

# Install Laravel if not exists
if [ ! -f artisan ]; then
    echo "📦 Cài đặt Laravel..."
    docker-compose exec php composer create-project laravel/laravel . --prefer-dist
fi

# Install Composer dependencies
echo "📦 Cài đặt dependencies..."
docker-compose exec php composer install

# Generate application key
echo "🔑 Tạo application key..."
docker-compose exec php php artisan key:generate

# Install JWT
echo "🔐 Cài đặt JWT Authentication..."
docker-compose exec php composer require tymon/jwt-auth
docker-compose exec php php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker-compose exec php php artisan jwt:secret

# Install other packages
echo "📦 Cài đặt các packages bổ sung..."
docker-compose exec php composer require spatie/laravel-permission
docker-compose exec php composer require spatie/laravel-activitylog
docker-compose exec php composer require intervention/image
docker-compose exec php composer require maatwebsite/excel

# Publish package configurations
echo "⚙️ Publish package configurations..."
docker-compose exec php php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
docker-compose exec php php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
docker-compose exec php php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"

# Run migrations
echo "🗄️ Chạy migrations..."
docker-compose exec php php artisan migrate

# Create storage link
echo "🔗 Tạo storage link..."
docker-compose exec php php artisan storage:link

# Set permissions
echo "🔒 Thiết lập permissions..."
docker-compose exec php chmod -R 775 storage
docker-compose exec php chmod -R 775 bootstrap/cache

# Clear cache
echo "🧹 Xóa cache..."
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan route:clear
docker-compose exec php php artisan view:clear

echo "✅ Setup hoàn tất!"
echo "🌐 Ứng dụng có thể truy cập tại: http://localhost"
echo "📧 Mailhog có thể truy cập tại: http://localhost:8025"
echo "🔧 Để vào PHP container: docker-compose exec php bash"
