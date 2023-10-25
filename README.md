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

### Start the development server

```shell
./vendor/bin/sail up
```

-   navigate to http://localhost/admin
-   username: test@test.com
-   password: test

### Build frontend assets

```shell
./vendor/bin/sail npm watch
```

### Run Tests

-   We only test the crawler
-   Others are already tested by the framework/library

```shell
./vendor/bin/sail test
```
