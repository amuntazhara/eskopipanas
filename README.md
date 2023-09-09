# eskopipanas
composer install
npm i
php artisan key:generate
php artisan migrate
php artisan db:seed UserSeeder
php artisan db:seed SubscriptionSeeder
php artisan db:seed DaftarBOSeeder
php artisan optimize

# tab cmd 1 (DEVELOPMENT/TESTING)
npm run dev
# tab cmd 1 (PRODUCTION/BUILD)
npm run build

# tab cmd 2 (DEVELOPMENT/TESTING)
php artisan serve
