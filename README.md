# PHP/Docker

Experimental set of Docker images for PHP projects. Inspired by PHP UK 2019 talk [Massively Scaled High Performance Web Services][slides] with PHP by Demin Yin.

The image set includes:

 - a web server base image with Nginx, PHP-FPM, Composer and Supervisor
 - a worker base image with PHP CLI and Composer

All PHP images have MySQL and MongoDB extensions enabled.

**Please note**: this is a proof of concept, not production-ready stuff!

## SSL

Although it may be tempting to embed SSL configuration and certs within web images, it does make more sense to let them serve HTTP and have a frontend proxy as SSL endpoint on the deployment server.

## TODO

 - [-] Move Nginx host and port settings within ENV vars
       Nginx conf does not support env vars, needs to customize single vhost file or use substitution scripts
 - [x] Use web and worker as base images and build separate web app and worker
 - [x] Use Monolog with worker and send to stdout/err
 - [x] Use non-root user for worker
 - [x] Build basic Slim app
 - [x] Attach MySQL and MongoDB servers
 - [ ] Improve Nginx and PHP config
 - [ ] Use wait code for mysql/mongo to start
 - [ ] Test SSL with proxy

[slides]: https://joind.in/event/php-uk-conference-2019/massively-scaled-high-performance-web-services-with-php
