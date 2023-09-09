# eskopipanas
composer install
npm i
php artisan key:generate
php artisan migrate
php artisan db:seed UserSeeder
php artisan db:seed SubscriptionSeeder
php artisan db:seed DaftarBOSeeder
php artisan optimize

# tab terminal 1 (development/testing)
npm run dev
# tab terminal 1 (production/testing)
npm run build

# tab terminal 2 (development/testing)
php artisan serve
