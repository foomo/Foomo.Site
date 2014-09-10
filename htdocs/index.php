<?php
// @todo: move to apache
ini_set('opcache.enable', 'off');

echo \Foomo\Site\Router::run();
