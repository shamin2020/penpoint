<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load the .env file from your project root.
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


use core\Application as Application;


(new Application())->run();
