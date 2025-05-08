# Tricount

## start

```bash
# generate key pair for JWT in config/jwt/
$ php bin/console lexit:jwt:generate-keypair
$ symfony server:start
$ php bin/console doctrine:database:create
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:fixtures:load
```

## authentication

1. get the token

```bash
$ curl -X POST -h "Content-Type: application/json" http://127.0.0.1/api/signin/password - d '{"username": , "password": "changeme"}'
```

1. user logs in at /api/signin/password

```json
{
  "access_token": "xxx",
  "refresh_token": "xxx",
  "refresh_token_expiration": "xxx"
}
```

2. upon login, a JWT token is delivered as well as a refreshToken and the expiration time of the latter token
3. user must use JWT to make any api request
4. once authenticated, user make a call to /api/me to fetch more user data
5. user can refresh its token at /api/token/refresh
