<?php
/* @var $view \Foomo\MVC\View */
/* @var $exception \Exception */

if ($exception instanceof \Foomo\Site\Exception\Content) {
	$content = $view->partial('exception/' . $exception->getCode(), ['exception' => $exception]);
} else {
	$content = $view->partial('exception/500', ['exception' => $exception]);
}
echo $view->partial('body', ['content' => $content]);