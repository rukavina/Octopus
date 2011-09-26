<?php
$pid = pcntl_fork();
if ($pid == -1) {
    die('could not fork');
} else if ($pid) {
    // we are the parent
    //pcntl_wait($status); //Protect against Zombie children
    echo "Exiting parent process " . getmypid() . "\n";
    exit;
} else {
    echo "My PID is " . getmypid() . "\n";
}

$pid = getmypid();
echo "Running with PID: $pid\n";

//write pid file
file_put_contents("/var/run/$daemonName.pid",$pid);

//Without this directive, PHP won’t be able to handle the signals
declare(ticks=1);

//registering the handler
pcntl_signal(SIGTERM, "sigHandler");

ini_set("error_append_string","\n");
ini_set("log_errors","1");
ini_set("error_log","/var/log/octopus/$daemonName-error.log");
ini_set("error_reporting",E_ALL);
ini_set("display_errors","0");