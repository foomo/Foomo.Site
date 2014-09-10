<?php
/* @var $view \Foomo\MVC\View */
/* @var $exception \Exception */

$content = $view->partial('exception', ['exception' => $exception]);
echo $view->partial('body', ['content' => $content]);