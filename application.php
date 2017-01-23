<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/helpers.php';

set_time_limit(2*60*60);

use Symfony\Component\Console\Application;

$application = new Application();

$commandspath = __DIR__.'/app/commands';
$commands = scandir($commandspath);

foreach($commands as $commandpath) {
    if ($commandpath == '.')
        continue;
    
    if ($commandpath == '..')
        continue;
    
    require_once $commandspath.'/'.$commandpath;
    
    $commandname = pathinfo($commandpath, PATHINFO_FILENAME);
    
    if ($commandname == 'Command')
        continue;
    
    $command = new $commandname;
    
    $application->add($command);
}

$application->run();