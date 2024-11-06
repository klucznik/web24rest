# Symfony REST API


### Install

```bash
git clone https://github.com/klucznik/web24rest
cd web24rest
composer install
```

#### Database
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### Run local server with Symfony app
```bash
symfony server:start
```

