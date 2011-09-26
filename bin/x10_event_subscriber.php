<?php
//Handler
function sigHandler($signo) {
    switch ($signo) {
        case SIGTERM:
            echo "Going to stop PID:" . getmypid() . "\n";
            Octopus_X10_Event_Subscriber_Service::getInstance()->stop();
            break;
        default:
    }
}
$daemonName = 'octopus-sub';

include_once 'include/daemon.php';
include_once 'include/bootstrap.php';

//After that, to keep the daemon (the child process) from termination let’s loop it:
Octopus_X10_Event_Subscriber_Service::getInstance($configuration->toArray())->run();
