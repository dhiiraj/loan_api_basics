## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Project Setup

# deply.sh file have all command to run project.

    Before running script make sure to update .env file with Database name, host, user and password.
    To run project in one go run deploy.sh (file permission chmod -R 777) from project root.
    ./deplot.sh

## manual run

# Install php libraries and dependencies.

> composer install
> php artisan key:generate

# Update .env file with Database name. database host, database user and database password.

> php artisan migrate

# To run phpUnit tests

> php artisan test

# run project

> php artisan serve

# Open borwser and type : localhost:8000 or 127.0.0.1:8000

## API endpoints with param shared postman collection.

> All API will accept Bearer token (login API will return token) except registeration API

## once application fully running then can test phpunit tests.

> php artisan test

## Done.
