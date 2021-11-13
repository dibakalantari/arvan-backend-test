# Arvan Cloud Backend Test

This repository is an extended version of https://github.com/gothinkster/laravel-realworld-example-app

# Installation

In order to run this project locally you need to proceed following steps:

```shell
git clone https://github.com/dibakalantari/arvan-backend-test.git
cd arvan-backend-test
cp .env.example .env
docker-compose up -d
docker-compose exec app bash
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```
