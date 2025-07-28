echo "Update dari Github"
git reset --hard
git clean -fd
git pull origin main

echo "Bersihkan Laravel"
php artisan config:cache
php artisan route:cache
php artisan view:clear

echo "Selesai!"