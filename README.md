# Laravel API Test

## Requirements

To run this project you need to have the following installed:

1. [PHP](https://www.php.net/) version 8.3.12
3. [Composer](https://getcomposer.org/download/) version 2.8
4. [GNU Make](https://www.gnu.org/software/make/)
5. [MySQL](https://mysql.com) version 5.7

## Initiate The Project

Before start you need to setup the database and configure .env

After that, execute:

```
make prepare
```
This will run composer update and migrate the table

## Running

To run the app:

```
make run-app
```

to run scheduller:

```
make run-scheduller
```

You should be able to access the API at http://localhost:8000
