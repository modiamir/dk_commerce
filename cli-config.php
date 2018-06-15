<?php

use Digikala\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/vendor/autoload.php';


$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$kernel = new Kernel('dev', true);
$kernel->boot();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($kernel->getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class));