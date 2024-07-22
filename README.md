# Things

## Installation

Clone the repo locally:

```shell
git clone git@github.com:jworksuk/things.git
cd things
```

Copy .env file

```shell
cp .env.example .env
```

Build and start the Docker containers:
```shell
docker compose up --build -d
```

Install Composer dependencies:

```shell
docker compose exec -it php-fpm composer install
```

Run database migrations

```shell
docker compose exec -it php-fpm composer run db-bootstrap
```

## TODO
- [ ] Error handling
- [ ] Auth Token
- [ ] Events
- [ ] Console app
- [ ] React App