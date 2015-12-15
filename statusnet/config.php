<?php
if (!defined('STATUSNET') && !defined('LACONICA')) { exit(1); }

$config['site']['name'] = 'statusnet';

$config['site']['server'] = 'localhost';
$config['site']['path'] = 'workspace/experiments/statusnet'; 

$config['db']['database'] = 'mysqli://statusnet:statusnet@localhost/statusnet';

$config['db']['type'] = 'mysql';

