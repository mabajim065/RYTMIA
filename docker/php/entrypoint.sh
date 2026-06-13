#!/bin/bash
set -e

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  RYTMIA — Iniciando contenedor"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

#  1. Instalar dependencias PHP (producción)
echo "[1/7] Instalando dependencias PHP..."
composer update --no-dev --optimize-autoloader --no-interaction --quiet

#2. Instalar dependencias Node y compilar assets Vite
echo "[2/7] Compilando assets Vite + TailwindCSS..."
npm ci --quiet
npm run build

#3. Generar clave de aplicación si no existe 
echo "[3/7] Verificando APP_KEY..."
php artisan key:generate --force

#4. Ejecutar migraciones
echo "[4/7] Ejecutando migraciones..."
php artisan migrate --force

#  5. Cachear configuración, rutas y vistas (producción) 
echo "[5/7] Cacheando configuración y rutas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

#  6. Permisos finales 
echo "[6/7] Ajustando permisos de storage..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

#  7. Queue worker en segundo plano 
echo "[7/7] Iniciando queue worker en segundo plano..."
php artisan queue:work --daemon --sleep=3 --tries=3 &

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  ✅  Listo — Iniciando PHP-FPM"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

exec php-fpm
