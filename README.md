# Requirements
1. Node.js
2. Composer
3. Docker

## Usage
Clone / Download ZIP and extract
# Backend
1. "cd" into EuromBackend ```cd EuromBackend```
2. Edit docker-compose.yml
   - phpmyadmin <br>```MYSQL_USERNAME: root```<br>```MYSQL_ROOT_PASSWORD: passpass```
   - mysql <br>```MYSQL_ROOT_PASSWORD: 'passpass'```<br>```
      MYSQL_DATABASE: 'euromdb'```
3. Install packages ```composer install```
4. Start docker containers ```docker compose up -d```
5. Open ```http://localhost:8080``` and login with your credentials from step 2.
6. Make your DB structure
7. Generate models and routes ```composer generateAll```
8. Generate keys for JWT ```composer generateKeys```
