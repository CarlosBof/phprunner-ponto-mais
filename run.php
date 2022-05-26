<?php

set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

require_once 'vendor/autoload.php';

use Token\Runner;

putenv('WEBDRIVER_CHROME_DRIVER=C:/Apps/AppPHP/phprunner-ponto-mais/config/driver/chromedriver.exe');

$drv = new Runner(require_once 'config/config.inc.php');
$drv->execute();