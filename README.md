# Simple crawler

## Demo

-   [https://crawler.vortechron.com/](https://crawler.vortechron.com/)

## Local Development

### Requirements

-   [Docker](https://www.docker.com/)
-   [Docker Compose](https://docs.docker.com/compose/)

### Tech Stack

-   [Laravel](https://laravel.com/)
-   [Tailwind CSS](https://tailwindcss.com/)
-   [Alpine.js](https://alpinejs.dev/)
-   [Livewire](https://laravel-livewire.com/)
-   [Filamentphp](https://filamentphp.com/)
-   [PHPUnit](https://phpunit.de/)

### Install

```shell
./initialize
```

Running this script will:

-   install composer dependencies
-   install npm dependencies
-   create a .env file
-   generate an application key
-   build the docker containers
-   run the database migrations
-   create initial user

### Start the development server

```shell
./vendor/bin/sail up

# for background process
./vendor/bin/sail artisan horizon
```

-   navigate to http://localhost/admin
-   username: admin@admin.com
-   password: secret

### Build frontend assets

```shell
./vendor/bin/sail npm watch
```

### Run Tests

-   We only test the crawler
-   Other tools are already tested by the framework/library

```shell
./vendor/bin/sail test
```
