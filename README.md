# PHP/Docker

This repo contains an experimental set of Docker images for deploying PHP projects using Docker.

It was inspired by Demin Yin's PHP UK 2019 talk [Massively Scaled High Performance Web Services with PHP][slides].

The image set includes:

 - a web server base image with Nginx, PHP-FPM, Composer and Supervisor
 - a worker base image with PHP CLI and Composer
 - a simple web app example that runs with the server image
 - a simple background CLI app that runs with the worker image

All images are based on Alpine GNU/Linux in order to be as small as possible, and have MySQL and MongoDB PHP extensions enabled.

## Testing the example apps

### 1. Build the base images without username

```console
$ cd base/web && docker build . -t php-server:7.3.9-alpine; cd ../..
```

```console
$ cd base/worker && docker build . -t php-worker:7.3.9-alpine; cd ../..
```

### 2. Start the stack

```console
$ docker-compose up --build [-d]
```

### 3. Check that all is working properly

The worker script should emit a log line every few seconds.

The web app listen by default on `http://localhost:8000`, it exposes this endpoints:

 - `/`: simple hello world
 - `/info.php`: shows PHP configuration
 - `/mysql`: connects to MySQL and fetches some sample data
 - `/mongodb`: connects to MongoDB and fetches some sample data

## Simple and naive production scenario

**Please note**: I did my best to configure the images with a proper security level, but I haven't yet tested them with an actual production environment, so if you want to use it in production take your time to test and tweak the system!

The scripts `build/web` and `build/worker` will package your web and console apps into Docker images adding a versioning tag based on the current timestamp. The latest version tag is saved into `build/web.version` and `build/worker.version`.

```console
REPOSITORY          TAG                    IMAGE ID            CREATED              SIZE
app-worker          20190911104230         3f3f297562a6        About a minute ago   120MB
app-web             20190911104105         d451986e42f5        2 minutes ago        170MB
```

The images can then deployed to either your private Docker registry (i.e. `docker push app-web:20190911104105`) or directly to your production server using Docker `save` command:

```console
$ docker save app-web:20190911104105 | gzip > build/app-web-20190911104105.tar.gz
$ scp build/app-web-20190911104105.tar.gz you@yourserver:/some/path/app-web-20190911104105.tar.gz
```

On your production server you can then `load` the new image into Docker:

```console
$ ssh you@yourserver 'docker load /some/path/app-web-20190911104105.tar.gz'
$ scp build/web.version you@yourserver:/some/path/web.version
```

And start the new version using a script similar to the provided `prod` example, which reads the image tags from the `.version` files and set the needed environment variables.

```console
$ ssh you@yourserver 'cd /some/path && ./prod stop && ./prod start'
```

All this can be done manually or by a CI/CD solution like [Bitbucket Pipelines](https://bitbucket.org/product/features/pipelines) or [Github Actions](https://github.com/features/actions).

## SSL and Proxy

Although it may be tempting to embed SSL configuration and certificates within web images, it does make more sense to let them serve HTTP on a custom port (i.e. 8000) and have a frontend proxy as SSL endpoint on the deployment server. This technique also will allow to show a maintenance page during the short `stop/start` cycle on update.

[slides]: https://joind.in/event/php-uk-conference-2019/massively-scaled-high-performance-web-services-with-php
