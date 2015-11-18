<?php

use Chorizon\TheServers\Task;
use PhangoApp\PhaModels\Webmodel;

Webmodel::load_model('vendor/chorizon/mail_unix/models/models_mail');
Webmodel::load_model('vendor/chorizon/theservers/models/models_servers');

function delete_domainConsole() 
{

    //Make standard class method with this.

    Task::make_simple_petition_ssh(['category' => 'mail', 'module' => 'mail_unix', 'script' => 'delete_domain.py'], 'callback');
    
    //Insert in database the data
    
    /*mail_server_unix
    | server             | int(11)      | NO   | MUL | 0       |                |
    | quota              | int(11)      | NO   |     | 0       |                |
    | num_accounts       | int(11)      | NO   |     | 0       |                |
    | domain             | varchar(255) | NO   |     | 0       |                |
    | user               | int(11)      | NO   | MUL | 0       |                |
    | status             | int(1)       | NO   |     | 0       |                |
    */
    
    
}

function callback($arr_args, $arr_extra_args)
{
    settype($arr_extra_args['domain_id'], 'integer');
    
    if($arr_extra_args['domain_id']>0)
    {
    
        Webmodel::$model['mail_server_unix']->set_conditions(['WHERE IdMail_server_unix=?', [$arr_extra_args['domain_id']]]);
    
        Webmodel::$model['mail_server_unix']->delete();
    
    }

    /*

    if(!Webmodel::$model['mail_server_unix']->insert(array('server' => $arr_extra_args['server_id'], 'quota' => $arr_args['quota'], 'num_accounts' => $arr_args['num_accounts'], 'domain' => $arr_args['domain'], 'user' => $arr_extra_args['user_id'], 'status' => 1)))
    {
    
        Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error: sorry, i add the new domain to the server but i cannot create the new row in database '.Webmodel::$model['mail_server_unix']->std_error, 'ERROR' => 1, 'CODE_ERROR' => 3, 'PROGRESS' => 100));
    
    }
    */

}

?>
