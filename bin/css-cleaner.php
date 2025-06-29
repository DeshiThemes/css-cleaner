#!/usr/bin/env php
<?php

$autoload = __DIR__ . '/../../autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "✖ Composer autoload not found. Run `composer install` first.\n");
    exit(1);
}

if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    fwrite(STDERR, "✖ PHP 8.1 or higher is required. Current version: " . PHP_VERSION . "\n");
    exit(1);
}

require $autoload;

use DeshiThemes\CssCleaner\Commands\OptimizeCssCommand;
use Symfony\Component\Console\Application;

$app = new Application('🌈 CSS Cleaner', '1.1.0');
$app->add(new OptimizeCssCommand());
$app->run();
