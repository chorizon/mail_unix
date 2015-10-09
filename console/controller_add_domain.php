<?php

use GuzzleHttp\Client;
use Chorizon\TheServers\Task;
use PhangoApp\PhaModels\Webmodel;

define('NO_JSON_RETURNED', 1);
define('NO_AUTHENTICATED', 2);
define('SCRIPT_NOT_EXISTS_IN_DB', 3);

function add_domainConsole() 
{

    //Make standard class method with this.

    list($task_id, $arr_task)=Task::daemonize();

    if($task_id!=0)
    {
    
        //use guzzle for send message to server with ca.crt and ca.key
        
        //Save results in database, when you go to 100, kill the script saving the result. 
        //If no answered, error.
        
        try {
        
            $client = new Client(['base_uri' => 'https://'.$arr_task['ip'].':'.PASTAFARI_PORT.'/pastafari/'.SECRET_KEY_PASTAFARI]);
            
            //?category=email&module=email&script=add_account
            
            $arr_args=unserialize($arr_task['arguments']);
            
            $arr_query=['category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain'];
                    
            foreach($arr_args as $key_task => $task)
            {
            
                $arr_query[$key_task]=$task;
            
            }
            
            $response = $client->request('GET', '', [ 'query' => $arr_query, 'verify' => PASTAFARI_SSL_VERIFY, 'cert' => PASTAFARI_SSL_CERT ]);
            
            $code = $response->getStatusCode(); // 200
            $reason = $response->getReasonPhrase(); // OK
            $uuid='';
            
            if($code!=200)
            {
            
                Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error, cannot execute the task: '.$reason, 'ERROR' => 1, 'CODE_ERROR' => 1, 'PROGRESS' => 100));
            
            }
            else
            {
            
                $body = $response->getBody();
                
                if(($arr_body=json_decode($body, true)))
                {
                
                    $arr_body['task_id']=$task_id;
                    
                    $uuid=$arr_body['UUID'];
                    
                    Task::log_progress($arr_body);
                
                }
                else
                {
                
                    Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error, i don\'t understand the message from server: '.$body, 'ERROR' => 1, 'CODE_ERROR' => NO_JSON_RETURNED, 'PROGRESS' => 100));
                    
                    die;
                
                }
                
                //If all fine, make loop and send message for obtain progress. 500 miliseconds.
                
                $done=false;
                
                $client_progress = new Client(['base_uri' => 'https://'.$arr_task['ip'].':'.PASTAFARI_PORT.'/pastafari/check_process/'.SECRET_KEY_PASTAFARI.'/'.$uuid]);
                
                $progress=0;
                
                while(!$done)
                {
                
                    //If timeout is excesive, kill the script?.
                
                    sleep(1);
                    
                    //Create method for obtain progress
                    
                    $response = $client_progress->request('GET', '', [ 'verify' => PASTAFARI_SSL_VERIFY, 'cert' => PASTAFARI_SSL_CERT ]);
                
                    if($code!=200)
                    {
                    
                        Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error, cannot execute the task: '.$reason, 'ERROR' => 1, 'CODE_ERROR' => 1, 'PROGRESS' => 100));
                        
                        die;
                    
                    }
                    else
                    {
                    
                        $body = $response->getBody();
                        
                        if(($arr_body=json_decode($body, true)))
                        {
                        
                            settype($arr_body['PROGRESS'], 'integer');
                            
                            if($arr_body['PROGRESS']!=$progress)
                            {
                        
                                $arr_body['task_id']=$task_id;
                                
                                Task::log_progress($arr_body);
                             
                                $progress=$arr_body['PROGRESS'];
                             
                            }
                            
                            //If 100, the script is finished and i can die
                            
                            if($arr_body['PROGRESS']==100)
                            {
                            
                                break;
                            
                            }
                        
                        }
                        else
                        {
                        
                            Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error, i don\'t understand the message from server: '.$body, 'ERROR' => 1, 'CODE_ERROR' => NO_JSON_RETURNED, 'PROGRESS' => 100));
                            
                            die;
                        
                        }
                
                    }
                
                }
            
            }
        }
        catch (Exception $e) {
        
            Task::log_progress(array('task_id' => $task_id, 'MESSAGE' => 'Error, cannot execute the task: '.$e->getMessage(), 'ERROR' => 1, 'CODE_ERROR' => NO_JSON_RETURNED, 'PROGRESS' => 100));
        
        }

    }
        
}

?>
