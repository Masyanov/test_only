## О проекте

тестовое задание от Only - https://docs.google.com/document/d/1nhGg4501YX1Ml9M0s-5SmfmsYxkX_14PPwaNc-e2Rh0/edit?tab=t.0#heading=h.2pazn2o8s5ja

Характеристики проекта:
- Laravel 12 - http://localhost:8080/
- MySql 8.0
- adminer для визуального управления базой данных - http://localhost:8081/
- Документация Swaager доступна по адресу http://localhost:8080/api/documentation

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

``` composer install ```

``` cp .env.example .env ```

``` docker-compose exec app php artisan key:generate ```

``` docker-compose exec app php artisan migrate ```

``` docker-compose exec app php artisan db:seed ```

