<?php

use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\CoreFields\ForeignKeyField;
use PhangoApp\PhaModels\CoreFields\IntegerField;
use PhangoApp\PhaModels\CoreFields\CharField;
use PhangoApp\PhaModels\CoreFields\BooleanField;

Webmodel::load_model('vendor/chorizon/theservers/models/models_servers');
Webmodel::load_model('vendor/chorizon/theusers/models/models_theusers');

$mail_server=new Webmodel('mail_server_unix');

$mail_server->register('domain', new CharField(255), true);

$mail_server->register('user', new ForeignKeyField(Webmodel::$model['theuser']), true);

$mail_server->register('server', new ForeignKeyField(Webmodel::$model['server']), true);

$mail_server->register('quota', new IntegerField(11));

$mail_server->register('num_accounts', new IntegerField(11), false);

$mail_server->register('filesystem', new IntegerField(11), false);

$mail_server->register('status', new BooleanField(11));

?>