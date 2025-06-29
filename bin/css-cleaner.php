#!/usr/bin/env php
<?php

$autoload = __DIR__ . '/../../autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "<fg=red>✖ Composer autoload not found.</>\n");
    fwrite(STDERR, "Run: <fg=cyan>composer install</>\n");
    exit(1);
}

if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    fwrite(STDERR, "<fg=red>✖ PHP 8.1 or higher is required.</>\n");
    fwrite(STDERR, "Current version: <fg=yellow>" . PHP_VERSION . "</>\n");
    exit(1);
}

require $autoload;

use DeshiThemes\CssCleaner\Commands\OptimizeCssCommand;
use Symfony\Component\Console\Application;

$app = new Application('🌈 <fg=cyan>CSS Cleaner</>', '1.1.0');
$app->add(new OptimizeCssCommand());
$app->run();
