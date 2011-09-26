<?php
//Handler
function sigHandler($signo) {
    switch ($signo) {
        case SIGTERM:
            echo "Going to stop PID:" . getmypid() . "\n";
            Octopus_X10_Event_Publisher_Service::getInstance()->stop();
            break;
        default:
            echo "Catched signo:$signo\n";
            break;
    }
}

$daemonName = 'octopus-pub';

include_once 'include/daemon.php';
include_once 'include/bootstrap.php';

Octopus_X10_Event_Publisher_Service::getInstance($configuration->toArray())->run();
