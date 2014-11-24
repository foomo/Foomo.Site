<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
/* @var $region string */
/* @var $language string */

$buttons = [
	'viewList'      => 'View List',
	'viewDump'      => 'View Dump',
	'updateCaches'  => '<span class="fa fa-tachometer"></span> Updated all Content Caches',
	'restartServer' => '<span class="fa fa-power-off"></span> Restart the Content Server',
]
?>

<nav id="menuSub">
	<ul>
		<? foreach ($buttons as $action => $name): ?>
			<li>
				<?= $view->partial(
					'menuButton',
					[
						'url'        => $action,
						'name'       => $name,
						'parameters' => [$model->dimension],
					],
					'Foomo\\Frontend'
				); ?>
			</li>
		<? endforeach; ?>
	</ul>
</nav>
