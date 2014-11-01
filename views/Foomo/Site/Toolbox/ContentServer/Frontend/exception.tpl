<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
/* @var $exception \Exception */
?>

<?= $view->partial('menu'); ?>

<div id="appContent">
	<h1>ERROR: <small><?= $exception->getMessage(); ?></small></h1>
	<pre><?= $exception->getTraceAsString(); ?></pre>
</div>
