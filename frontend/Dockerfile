# =========================================================
# FRONTEND - Laravel 12 con Blade (consume la API)
# Construcción en dos etapas (multi-stage)
# =========================================================

# ---------- Etapa 1: compilar CSS y JS con Node ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Etapa 2: aplicación PHP con Apache ----------
FROM php:8.3-apache

# 1. Paquetes del sistema (el frontend NO usa base de datos)
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
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

# 6. Traer SOLO el resultado compilado de la etapa 1 (sin Node)
COPY --from=assets /app/public/build ./public/build

# 7. Laravel necesita escribir en storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 8. Puerto en el que escucha Apache
EXPOSE 80