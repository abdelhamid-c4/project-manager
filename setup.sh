#!/bin/bash
# =============================================================
# Project Manager — Full Setup Script
# Run this AFTER creating a fresh Laravel project with Breeze
# =============================================================

set -e

echo ""
echo "============================================"
echo "  Laravel Project Manager — Setup Script"
echo "============================================"
echo ""

# 1. Install Composer packages
echo "[1/7] Installing Composer packages..."
composer require spatie/laravel-permission
composer require laravel/breeze --dev

# 2. Install Breeze (Blade)
echo "[2/7] Scaffolding Breeze authentication..."
php artisan breeze:install blade

# 3. Publish Spatie config
echo "[3/7] Publishing Spatie Permission config..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 4. Build frontend assets
echo "[4/7] Building frontend assets..."
npm install && npm run build

# 5. Run migrations and seed
echo "[5/7] Running migrations..."
php artisan migrate:fresh --seed

# 6. Generate app key if missing
echo "[6/7] Ensuring app key exists..."
php artisan key:generate --ansi 2>/dev/null || true

# 7. Clear caches
echo "[7/7] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "============================================"
echo "  ✅ Setup complete!"
echo "============================================"
echo ""
echo "  Run: php artisan serve"
echo "  Visit: http://localhost:8000"
echo ""
echo "  Default accounts:"
echo "  admin@example.com   / password  (Admin)"
echo "  manager@example.com / password  (Project Manager)"
echo "  member@example.com  / password  (Team Member)"
echo "  client@example.com  / password  (Client)"
echo ""
