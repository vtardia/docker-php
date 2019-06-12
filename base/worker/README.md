# PHP/Worker image

Self-contained PHP/CLI image

## Build

```console
$ cd worker && docker build . -t [docker-username/]php-worker:7.3.5-alpine
```

```console
$ docker run --rm [docker-username/]php-worker:7.3.5-alpine
```

```console
$ docker login
$ docker push [docker-username/]php-worker:7.3.5-alpine
```
