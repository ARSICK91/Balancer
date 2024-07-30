# Установка проекта

## Предварительные требования

- Установленный Docker
- Установленный Docker Compose

## Установка

1. Клонируйте репозиторий:

   ```bash
   git clone git@github.com:ARSICK91/Balancer.git
   cd Balancer
   
3. ```
    docker-compose -f ./docker/docker-compose.yml build
4. ```
    docker-compose -f ./docker/docker-compose.yml up

4. ```
    docker-compose -f ./docker/docker-compose.yml exec php-fpm bash

5. ```
    composer install
 6. ```
    exit

7. ```
   docker exec -it main_bd mysql -u root -p
8. ```
   1234
9. ```
   SHOW DATABASES;

10. ```
    CREATE DATABASE test_balancer_bd;
11. ```
    exit
    
12. ```
    docker exec -it main_php php bin/console doctrine:migrations:migrate --no-interaction
13. ```
    docker-compose -f ./docker/docker-compose.yml down
    docker-compose -f ./docker/docker-compose.yml build
    docker-compose -f ./docker/docker-compose.yml up 
