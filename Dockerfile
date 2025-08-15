# ใช้ Image พื้นฐานของ PHP
FROM php:8.2-fpm

# ติดตั้ง System Dependencies ที่จำเป็น
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง PHP Extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้งค่า Working Directory
WORKDIR /var/www/html

# คัดลอกโปรเจกต์ Laravel
COPY . .

# ติดตั้ง Composer Dependencies
RUN composer install --no-dev --optimize-autoloader

# ตั้งค่าสิทธิ์ของ Storage และ Cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# สั่งรัน Nginx (ถ้าจำเป็น) และ PHP-FPM
# ส่วนนี้จะจัดการโดย Render Service (Web Service)
# Render จะใช้ Process Command ของคุณ (php artisan serve) ในการรัน
# ซึ่งจะถูกตั้งค่าในหน้า Deploy ของ Render เอง
# ดังนั้นใน Dockerfile นี้จึงไม่ต้องมี CMD หรือ ENTRYPOINT เพื่อรัน Server โดยตรง
