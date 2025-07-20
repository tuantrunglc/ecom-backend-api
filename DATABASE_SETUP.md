# EcoMan E-Commerce Backend - Database Setup

## Tổng quan
Hệ thống backend đã được setup hoàn chỉnh với dữ liệu mẫu bao gồm:

## 📊 Dữ liệu đã tạo

### 👥 Users & Profiles
- **1 Admin**: admin@ecoman.com / admin123
- **1 SubAdmin**: subadmin@ecoman.com / subadmin123  
- **5 Regular Users**: user1@example.com đến user5@example.com / user123
- **7 User Profiles**: Thông tin chi tiết cho tất cả users

### 📂 Categories (25 categories)
**Parent Categories (5):**
1. Electronics
2. Fashion
3. Home & Garden
4. Sports & Outdoors
5. Books & Media

**Child Categories (20):**
- Electronics: Smartphones, Laptops, Tablets, Headphones
- Fashion: Men's Clothing, Women's Clothing, Shoes, Accessories
- Home & Garden: Furniture, Kitchen, Garden Tools, Home Decor
- Sports & Outdoors: Fitness Equipment, Outdoor Gear, Sports Apparel, Team Sports
- Books & Media: Books, Movies & TV, Music, Games

### 🛍️ Products (10 products)
- iPhone 15 Pro (Featured, On Sale)
- Samsung Galaxy S24 (Featured)
- MacBook Pro 16" (Featured)
- Dell XPS 13 (On Sale)
- Classic Cotton T-Shirt
- Denim Jeans (On Sale)
- Summer Dress (Featured)
- Modern Office Chair (On Sale)
- Yoga Mat Premium
- The Art of Programming

## 🗄️ Database Structure

### Bảng chính:
- `users` - Thông tin cơ bản của user
- `user_profiles` - Thông tin chi tiết của user (phone, address, etc.)
- `categories` - Danh mục sản phẩm (có hỗ trợ parent-child)
- `products` - Sản phẩm
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng

### Bảng hệ thống:
- `roles` - Vai trò (admin, subadmin, user)
- `permissions` - Quyền hạn
- `model_has_roles` - Liên kết user với role
- `activity_log` - Log hoạt động

## 🔗 API Endpoints

### Authentication
- `POST /api/auth/register` - Đăng ký
- `POST /api/auth/login` - Đăng nhập
- `POST /api/auth/logout` - Đăng xuất (protected)
- `GET /api/auth/me` - Thông tin user (protected)

### Public Endpoints
- `GET /api/products` - Danh sách sản phẩm
- `GET /api/products/{id}` - Chi tiết sản phẩm
- `GET /api/categories` - Danh sách danh mục
- `GET /api/categories/{id}` - Chi tiết danh mục

### Admin Endpoints (protected)
- `GET /api/admin/users` - Quản lý users
- `POST /api/admin/users` - Tạo user mới
- `POST /api/admin/products` - Tạo sản phẩm
- `PUT /api/admin/products/{id}` - Cập nhật sản phẩm
- `DELETE /api/admin/products/{id}` - Xóa sản phẩm

## 🔐 Login Credentials

### Admin
- Email: admin@ecoman.com
- Password: admin123
- Permissions: Toàn quyền

### SubAdmin  
- Email: subadmin@ecoman.com
- Password: subadmin123
- Permissions: Quản lý products, categories, orders, reports

### Regular Users
- user1@example.com / user123 (Nguyen Van A - Hanoi)
- user2@example.com / user123 (Tran Thi B - Da Nang)
- user3@example.com / user123 (Le Van C - Can Tho)
- user4@example.com / user123 (Pham Thi D - Hai Phong)
- user5@example.com / user123 (Hoang Van E - Hue)

## 🚀 Commands hữu ích

```bash
# Xem tổng quan dữ liệu
php artisan data:overview

# Xem chi tiết users
php artisan check:users

# Reset database và tạo lại dữ liệu
php artisan migrate:fresh --seed
```

## 📝 Notes
- Tất cả users đều có profile đầy đủ thông tin
- Products có hình ảnh placeholder
- Categories được tổ chức theo cấu trúc parent-child
- Hệ thống roles & permissions đã được setup
- JWT authentication đã được cấu hình
- Activity logging đã được kích hoạt

## ✅ Status
Hệ thống đã sẵn sàng để test và phát triển tiếp!