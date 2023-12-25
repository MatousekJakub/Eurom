# Requirements

1. Node.js
2. Composer
3. Docker Engine

# Installation

Clone / Download ZIP and extract

## Backend

1. "cd" into EuromBackend `cd EuromBackend`
2. Edit docker-compose.yml
   - phpmyadmin <br>`MYSQL_USERNAME: root`<br>`MYSQL_ROOT_PASSWORD: passpass`
   - mysql <br>`MYSQL_ROOT_PASSWORD: 'passpass'`<br>`MYSQL_DATABASE: 'euromdb'`
3. Install packages `composer install`
4. Start docker containers `docker compose up -d`
5. Open phpMyAdmin `http://localhost:8080` and login with your credentials from step 2.
6. Make your DB structure
   > [!WARNING]
   > Do not create a table called "login"
7. Generate models and routes `composer generateAll`
8. Generate keys for JWT `composer generateKeys`
9. Implement `\Model\Auth\LoginUserFactory` at `app/model/auth/LoginUserFactory.php`
   - `getLoginUserById(int $id)`
   - `getLoginUserByLoginPass(string $login, string $pass)`

## Frontend

1. "cd" into EuromFrontend `cd EuromFrontend`
2. Install packages `npm install`
3. Copy folder from `EuromBackend\exports\ts\model` into `EuromFrontend\src\model`
4. Run the development environment `npm run dev`
