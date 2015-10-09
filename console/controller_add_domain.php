<?php

use Chorizon\TheServers\Task;
use GuzzleHttp\Client;

define('NO_JSON_RETURNED', 1);
define('NO_AUTHENTICATED', 2);
define('SCRIPT_NOT_EXISTS_IN_DB', 3);

function add_domainConsole() 
{

    //Make standard class method with this.

    Task::make_simple_petition(['category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain']);
      
}

?>
