<?php

require ('../vendor/autoload.php');

if (!file_exists('../app/config/parameters.ini')) {
    exit('File config not exist');
}

$ctrl = new Controller(parse_ini_file('../app/config/parameters.ini'));
$ctrl->main();
