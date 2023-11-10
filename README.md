# Ecommerce Backend API


# Database:
- User
- Role
- Permission


# Documentation:

- Docker Compose
    - UP
        ``` bash
        docker compose up -d 
        ```
    - Down
        ``` bash
        docker compose down
        ```
    - Build
        ```bash
        docker compose up -d --build
        ```
    - Update
        ```bash
        docker compose run --rm composer update
        ```
    - npm run Dev
        ```bash
        docker compose run --rm npm run dev
        ```
    - Migrate
        ```bash
        docker compose run --rm artisan migrate
        ```
    - Test
        ```bash
        docker compose run --rm artisan test
        ```

- Documentation Generate
    ```bash
    docker compose run --rm artisan enum:docs  && docker compose run --rm artisan scribe:generate --force
    ```
- IDE Helper Generate
    ```bash
    docker compose run --rm artisan migrate:fresh --seed && docker compose run --rm artisan ide-helper:generate && docker compose run --rm artisan ide-helper:models --write --reset --write-mixin && docker compose run --rm artisan ide-helper:meta
    ```
- Deployer unlock
    ```bash
    docker compose run --rm php vendor/bin/dep deploy:unlock
    ```

- PHP Pint Fixer
    ```bash
    docker compose run --rm php ./vendor/bin/pint
    docker compose run --rm php ./vendor/bin/pint --test
    ```

- SQL init
    ```mysql
    CREATE DATABASE ECOMMERCE_DB;
    CREATE USER 'homestead'@'%' IDENTIFIED BY 'secret';
    GRANT ALL PRIVILEGES ON * . * TO 'homestead'@'%';
    FLUSH PRIVILEGES;
    ```

- Deploy NOTES:
    - generate ssh key using this command
  ```bash
  ssh-keygen -t rsa -C your_email@example.com
    ```
    - Add gitlab private key variable
    - generate cloudways app user access and link it with public ssh key
    - add public ssh key to gitlab repository keys
    - for more info
        - https://support.cloudways.com/en/articles/5124793-using-git-via-command-line

