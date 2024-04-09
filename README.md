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

### App once run well but not working now

It's possible that the application didn't stop properly the first time and that a worker is still running.

Try the following :
 - `symfony server:stop`
 - `symfony server:start`

### Logging is not working

You can try to make app more verbose using `-v`, `-vv` or `-vvv` (multiple logs level) when starting server.

Example : `symfony server:start -vvv` 
