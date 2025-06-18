## Get started
This is an project assignment of course SECJ3483-05 WEB TECHNOLOGY

## Client part
Install dependencies and run 
```bash
cd client
npm i
npm run dev
```

## Server part
1. Install dependencies
```bash
cd server
composer require slim/slim "^4.0"
composer require slim/psr7
composer require nyholm/psr7
composer require cboden/ratchet
composer dump-autoload
composer install
```

2. run this to run server 
```bash 
cd server
php -S localhost:8000 -t public
```

3. Enable Websocket 
```bash
cd server
php bin/websocket-server.php 
```

4. run sql script in /sql/setup.sql to create db in phpMyAdmin

