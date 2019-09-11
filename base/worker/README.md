# PHP/Worker image

Self-contained PHP/CLI image

## Build

In order to test the example apps you need to omit the `docker-username` part of the tags. Use your own Docker username if you want to publish your custom base images on your public or private Docker repository.

```console
$ cd base/worker && docker build . -t [docker-username/]php-worker:7.3.9-alpine; cd ../..
```

```console
$ docker run --rm [docker-username/]php-worker:7.3.9-alpine
```

```console
$ docker login
$ docker push [docker-username/]php-worker:7.3.9-alpine
```
