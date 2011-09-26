#!/usr/bin/php5
<?php
/**
 * php x10_command.php --function=on --address=A1 --param=5
 */

$cliOptions = array(
    'function|f=s' => 'X10 function: on, off, dim, bright...',
    'address|a=s' => 'Device address',
    'parameter|p-i' => 'Optional function parameter'
);

include_once 'include/bootstrap.php';

$commander = Octopus_X10_Command::getInstance($configuration->toArray());

if($getopt->getOption("function") == "status"){
    $output = $commander->getStatus();
}
else{
    $output = $commander->execute($getopt->getOption("function"), $getopt->getOption("address"),$getopt->getOption("parameter"));
}


print_r($output);
