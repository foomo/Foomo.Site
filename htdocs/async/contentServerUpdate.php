<?php

//connection handling
use Foomo\Site\Module;

ob_end_clean();

ob_start();
header("Connection: close");
header("Content-Length: " . ob_get_length());
ob_end_flush();
flush();

ignore_user_abort(true);

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

Module::getSiteContentServerProxyConfig()->getProxy()->update();