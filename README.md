# Devine: Part 1 (symfony)

This is part 1 of two eco systems that are integrated for demonstration purposes.

This is a PHP 8, MySQL 10 and Symfony 6.

## Setup

Now in your terminal, inside the root directory, 
run this command to install all the necessary modules.

```bash
composer install
```
Update the MySQL details inside /.env to match your own and
create your database.

```bash
php bin/console make:migration
```

Create the tables.

```bash
php bin/console doctrine:migrations:migrate
```

## Testing the application

Spin up the application using Symfony's built-in PHP server 
```bash
php -S 127.0.0.1:8080 -t public
```
and open [localhost]
(http://127.0.0.1:8080)

API routes for testing are:
api/v1/languages - GET
api/v1/languages - POST
api/v1/languages/{id} - PUT
api/v1/languages/{id} - DELETE

Add a few languages using the post route above.

You need to follow the steps in the second part of this demo series
to see the languages you added in Drupal 9.

## License
[MIT](https://choosealicense.com/licenses/mit/)
