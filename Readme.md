## Get started
This is an project assignment of course SECJ3483-05 WEB TECHNOLOGY

## Client part
Install dependencies and run 
```bash
npm i
npm run dev
```

## Server part
1. Install dependencies
```bash
composer require slim/slim "^4.0"
composer require slim/psr7
composer require nyholm/psr7
composer require cboden/ratchet
composer dump-autoload
composer install
```

2. run this to run server 
```bash 
php -S localhost:8000 -t public
```

3. run sql script in /sql/setup.sql to create db in phpMyAdmin

