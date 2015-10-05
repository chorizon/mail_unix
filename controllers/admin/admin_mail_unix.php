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
            
                if($model->mail_server_unix->insert($_POST))
                {
                
                    //Add ajax for connect
                    /*
                    ob_start();
                    
                    ?>
                    <script language="javascript">
                        $(document).ready( function () {
                        
                            //Ajax calling to make script
                        
                        });
                    </script>
                    <?php
                    
                    View::$header[]=ob_get_contents();
                    
                    ob_end_clean();
                    */
                    
                    $post=ModelForm::check_form($model->mail_server_unix->forms, $_POST);

                    $arr_server=$model->server->select_a_row($post['server']);
                    
                    print_r($arr_server);
                    
                    $server_to_call=$arr_server['ip'];
                    
                    $arguments='--domain '.$post['domain'];
                    
                    echo View::load_view(array('server_to_call' => $server_to_call, 'category' => 'mail', 'module' => 'mail_unix', 'script' => 'add_domain', 'arguments' => $arguments), 'ajax/ajaxserver', 'chorizon/pastafari');
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
        
        
    }

}

?>