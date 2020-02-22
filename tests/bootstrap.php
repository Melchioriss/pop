<?php

use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$envFiles = [
    __DIR__ . '/../.env.test',
    __DIR__ . '/../.env.test.local',
];

$dotEnv = new Dotenv(true);

foreach ($envFiles as $envFile) {
    if (file_exists($envFile)) {
        $dotEnv->load($envFile);
    }
}
