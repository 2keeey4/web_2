FROM php:8.2-apache

# Установка необходимых пакетов и PHP-расширений
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git \
    && docker-php-ext-install zip pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем весь проект
COPY . /var/www/html

# Устанавливаем зависимости
WORKDIR /var/www/html
RUN composer install

# Настраиваем DocumentRoot на public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Включаем mod_rewrite и разрешаем .htaccess
RUN a2enmod rewrite && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
