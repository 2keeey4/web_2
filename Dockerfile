FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    zip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip gd

RUN a2enmod rewrite && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html

COPY composer.json ./


RUN composer install --no-interaction --no-progress

COPY . .


RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/public

CMD ["apache2-foreground"]
