<?php

use Chorizon\TheServers\Task;
use PhangoApp\PhaModels\Webmodel;

function add_domainConsole() 
{

    //Make standard class method with this.

    list($arr_args, $arr_extra_args)=Task::make_simple_petition_ssh(['category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain.py']);
    
    //Insert in database the data
    
    
    
}

?>
