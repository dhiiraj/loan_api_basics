#install dependencies
echo "installing dependencies..."
composer install
echo "Generating key..."
php artisan key:generate
echo "Running migration...Please sure databse  is created and .env file updated"
php artisan migrate
echo "Running project..."
php artisan serve

