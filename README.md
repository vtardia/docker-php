# PHP/Docker

Experimental set of Docker images for PHP projects. Inspired by PHP UK 2019 talk [Massively Scaled High Performance Web Services][slides] with PHP by Demin Yin.

The image set includes:

 - a web server base image with Nginx, PHP-FPM, Composer and Supervisor
 - a worker base image with PHP CLI and Composer

All PHP images have MySQL and MongoDB extensions enabled.

**Please note**: this is a proof of concept, not production-ready stuff!

## TODO

 - [ ] Move Nginx host and port settings within ENV vars
 - [ ] Use web and worker as base images and build separate web app and worker
 - [ ] Use Monolog with worker and send to stdout/err
 - [ ] Use non-root user for worker
 - [ ] Build basic Slim app
 - [ ] Attach MySQL and MongoDB servers
 - [ ] Improve Nginx and PHP config


[slides]: https://joind.in/event/php-uk-conference-2019/massively-scaled-high-performance-web-services-with-php
