<?php

use Chorizon\TheServers\Task;

function add_domainConsole() 
{

    $task_id=Task::daemonize();

    if($task_id!=0)
    {
    
        //use guzzle for send message to server with ca.crt and ca.key
        
        //Save results in database, when you go to 100, kill the script saving the result. 
        //If no answered, error.
        
        while(true)
        {

            

        }

    }
        
}

?>
