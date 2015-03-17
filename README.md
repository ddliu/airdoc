# airdoc

Serve markdown document on the air.

## Features

- Documents are structured by directory
- No database

## Server

### PHP Builtin Server

```
make server
```
or

```
php -S localhost:8080 -t . router.php
```

### Nginx

See nginx.conf

## TODO

- Security
- Simple authentication
- Cache