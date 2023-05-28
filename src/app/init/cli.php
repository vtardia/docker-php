<?php

require __DIR__ . '/../vendor/autoload.php';

$container = new \DI\Container();

// Load dependencies
$services = glob(__DIR__ . '/services/*.php');
foreach ($services as $service) {
	require_once $service;
}
