<?php

use Chorizon\TheServers\Task;

function add_domainConsole() 
{

    //Make standard class method with this.

    Task::make_simple_petition_ssh(['category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain']);
      
}

?>
