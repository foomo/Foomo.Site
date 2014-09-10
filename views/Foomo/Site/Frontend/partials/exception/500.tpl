<?php
/* @var $view \Foomo\MVC\View */
/* @var $exception \Exception */
header('HTTP/1.0 500 Internal Server Error');
?>
<div class="jumbotron">
	<h1><?php echo $exception->getCode(); ?></h1>
	<p><?php echo $view->_e('EXCEPTION_500') ?></p>
</div>