# PHP/Nginx image

Self-contained PHP/Nginx image with Supervisor

## Build

```console
$ cd web && docker build . -t php-server:7.3.5-alpine
```

```console
$ docker run --rm -p 8080:80 php-server:7.3.5-alpine
```
