# PHP/Nginx image

Self-contained PHP/Nginx image with Supervisor

## Build

In order to test the example apps you need to omit the `docker-username` part of the tags. Use your own Docker username if you want to publish your custom base images on your public or private Docker repository.

```console
$ cd base/web && docker build . -t [docker-username/]php-server:7.4-alpine; cd ../..
```

```console
$ docker run --rm -p 8080:80 [docker-username/]php-server:7.4-alpine
```

```console
$ docker login
$ docker push [docker-username/]php-worker:7.4-alpine
```
