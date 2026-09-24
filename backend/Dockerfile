# =========================================================
# BACKEND - API Laravel 12 (Sanctum + MySQL)
# =========================================================

# Imagen base: PHP 8.3 con Apache ya incluido
FROM php:8.3-apache

# 1. Paquetes del sistema y extensión para conectarse a MySQL
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# 2. Apache: servir desde /public y permitir el .htaccess de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && a2enmod rewrite

# 3. Composer, copiado desde su imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Carpeta de trabajo dentro del contenedor
WORKDIR /var/www/html

# 5. Código del proyecto + dependencias de producción
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Laravel necesita escribir en storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 7. Puerto en el que escucha Apache
EXPOSE 80