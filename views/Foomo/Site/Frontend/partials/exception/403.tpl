<?php
/* @var $view \Foomo\MVC\View */
/* @var $exception \Exception */
header('HTTP/1.1 403 Forbidden');
?>
<div class="jumbotron">
	<h1><?php echo $exception->getCode(); ?></h1>
	<p><?php echo $view->_e('EXCEPTION_403') ?></p>
</div>