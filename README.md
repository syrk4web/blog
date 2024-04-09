# SETUP

## Principle

- `composer install` to retrieve all packages

- add `DATABASE_URL=<your_database>` on .env file

- (optional) create database with `php bin/console doctrine:database:create`

- add entities to database with `php bin/console doctrine:migrations:migrate`

## Fixes

### Database and ORM issues

Try to reset your database :

- `php bin/console doctrine:database:delete --force`

- `php bin/console doctrine:database:create`

- delete all `migrations/Version<num>.php` files

- `php bin/console make:migration`

- `php bin/console doctrine:migrations:migrate`

### App not rendering well / no file update

Try to clear cache using `php bin/console c:c` or `php bin/console clear:cache`.
