<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/helpers.php';


use Symfony\Component\Console\Application;

$application = new Application();

require __DIR__.'/app/commands/BuildCommand.php';
$application->add(new BuildCommand());

$application->setDefaultCommand('build', true);

$application->run();