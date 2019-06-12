# PHP/Nginx image

Self-contained PHP/Nginx image with Supervisor

## Build

```console
$ cd web && docker build . -t [docker-username/]php-server:7.3.5-alpine
```

```console
$ docker run --rm -p 8080:80 [docker-username/]php-server:7.3.5-alpine
```

```console
$ docker login
$ docker push [docker-username/]php-worker:7.3.5-alpine
```
