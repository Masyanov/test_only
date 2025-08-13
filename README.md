## О проекте

тестовое задание от Only

Характеристики проекта:
- Laravel 12 - http://localhost:8080/
- MySql 8.0
- adminer для визуального управления базой данных - http://localhost:8081/

Требуется:
- Node: 22.16.0
- npm: 10.9.2
- Apt: 2.4.13
- Composer: 2.8.5
- Docker: 28.2.2
- Docker Compose: 2.36.2
- PHP: 8.3.16

## Как развернуть в Docker

``` git clone https://github.com/Masyanov/test_only.git ```

``` cd test_only ```

``` docker-compose up -d --build ```

``` docker-compose exec app php artisan key:generate ```

``` docker-compose exec app php artisan migrate ```
