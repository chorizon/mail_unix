<?php

use PhangoApp\PhaView\View;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\Forms\SelectModelForm;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaUtils\MenuSelected;
use PhangoApp\PhaLibs\AdminUtils;
use PhangoApp\PhaLibs\SimpleList;
use PhangoApp\PhaI18n\I18n;
use PhangoApp\PhaRouter\Routes;
use Chorizon\TheServers\Task;

function Mail_unixAdmin()
{

    I18n::load_lang('mail_unix');

    Webmodel::load_model('vendor/chorizon/theusers/models/models_theusers');
    Webmodel::load_model('vendor/chorizon/theservers/models/models_servers');
    Webmodel::load_model('vendor/chorizon/mail_unix/models/models_mail');

    echo '<h2>'.I18n::lang('mail_unix', 'mail_unix', 'Email module').'</h2>';
    
    $model=&Webmodel::$m;
    
    #$arr_link_options[0]=array('link' => AdminUtils::set_admin_link('mail_unix', array('op' => 0)), 'text' => I18n::lang('mail_unix', 'domains', 'Domains'));
    
    settype($_GET['op'], 'integer');
    
    switch($_GET['op'])
    {
    
        default:
    
            echo '<p><a href="'.AdminUtils::set_admin_link('mail_unix', array('op' => 1)).'">'.I18n::lang('mail_unix', 'add_domain', 'Add new domain').'</a></p>';
        
        break;
        
        case 1:
        
            echo '<h3>'.I18n::lang('mail_unix', 'add_domain', 'Add new domain').'</h3>';
            
            $model->mail_server_unix->components['server']->form='PhangoApp\PhaModels\Forms\SelectModelForm';
            
            $model->mail_server_unix->components['user']->form='PhangoApp\PhaModels\Forms\SelectModelForm';
            
            $model->mail_server_unix->create_forms();
            
            $model->mail_server_unix->forms['server']->model=&$model->server;
            
            $model->mail_server_unix->forms['server']->field_value='id';
            
            $model->mail_server_unix->forms['server']->field_name='hostname';
            
            $model->mail_server_unix->forms['server']->conditions='WHERE type="mail_unix"';
            
            $model->mail_server_unix->forms['user']->model=&$model->theuser;
            
            $model->mail_server_unix->forms['user']->field_value='id';
            
            $model->mail_server_unix->forms['user']->field_name='username';
            
            $action=AdminUtils::set_admin_link('mail_unix', array('op' => 1));
            
            if(Routes::$request_method=='POST')
            {
            
                /*if($model->mail_server_unix->insert($_POST))
                {*/
                //Create task
                
                $_POST['status']=0;
                
                $post=ModelForm::check_form($model->mail_server_unix->forms, $_POST);
                
                //if($model->mail_server_unix->insert($_POST))
                if($post!=0)
                {

                    /*$arr_server=$model->server->select_a_row($post['server']);
                    
                    $server_to_call=$arr_server['ip'];
                    
                    $arguments='--domain '.$post['domain'];*/
                    
                    //Executing asinchronous script
                    
                    /*
                    $task->register('pid', new IntegerField(11), true);

                    $task->register('ip', new CharField(255), true);

                    $task->register('title', new CharField(255), true);
                    
                    $task->register('category', new CharField(255), true);

                    $task->register('module', new CharField(255), true);

                    $task->register('script', new CharField(255), true);

                    $task->register('arguments', new ArrayField(new CharField(255)), true);

                    $task->register('status', new BooleanField());
                    */
                    
                    //--domain, --user, --quota, --num_accounts, --filesystem
                    
                    $arr_user=Webmodel::$model['theuser']->select_a_row($post['user'], array('username'));
                    
                    $arguments=array('domain' => $post['domain'], 'quota' => $post['quota'], 'num_accounts' => $post['num_accounts'], 'filesystem' => $post['filesystem'], 'user' => $arr_user['username'] );
                    
                    //Beginning task
                    
                    $return_url=AdminUtils::set_admin_link('mail_unix', array('op' => 2) );
                    
                    Task::begin_task( array('server' => $post['server'], 'title' => I18n::lang('mail_unix', 'add_domain', 'Creating a new domain'), 'category' => 'chorizon', 'module' => 'mail_unix', 'script' => 'add_domain', 'arguments' => $arguments, 'extra_arguments' => array('user_id' => $post['user']), 'return' => $return_url) );
                    
                    //echo View::load_view(array('server_to_call' => $server_to_call, 'title' => I18n::lang('mail_unix', 'add_domain', 'Creating a new domain'), 'category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain', 'arguments' => $arguments), 'ajax/ajaxserver', 'chorizon/pastafari');
                    //Create user in 
                    //Routes::redirect(AdminUtils::set_admin_link('mail_unix', array('op' => 0)));
                
                }
                else
                {
            
                    ModelForm::set_values_form($model->mail_server_unix->forms, $_POST, $show_error=1);
                    
                    echo View::load_view(array($model->mail_server_unix->forms, array(), 'POST', $action, ''), 'forms/updatemodelform');
                    
                }
            
            }
            else
            {
            
                echo View::load_view(array($model->mail_server_unix->forms, array(), 'POST', $action, ''), 'forms/updatemodelform');
        
            }
        
        break;
        
        //Here obtain information about progress
        
        case 2:
        
            settype($_GET['task_id'], 'integer');
    
            $url_to_progress=AdminUtils::set_admin_link('mail_unix', array('op' => 3, 'task_id' => $_GET['task_id']) );
    
            echo View::load_view(array('url_to_progress' => $url_to_progress, 'title' => I18n::lang('mail_unix', 'add_domain', 'Creating a new domain'), 'category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain'), 'theservers/progress', 'chorizon/theservers');
        
        break;
        
        case 3:
        
            ob_end_clean();
        
            settype($_GET['task_id'], 'integer');
        
            $model->log_task->conditions='where task_id='.$_GET['task_id'];
            
            $model->log_task->order_by='order by id DESC';
            
            $model->log_task->limit='limit 1';
        
            $arr_log=$model->log_task->select_a_row_where();
            
            header('Content-type: text/plain');
            
            echo json_encode($arr_log);
            
            die;
        
        break;
        
        
    }

}

?>