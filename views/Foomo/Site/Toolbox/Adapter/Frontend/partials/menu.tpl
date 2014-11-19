<?php
/* @var $view \Foomo\Site\Toolbox\Adapter\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\Adapter\Frontend\Model */

$buttons = [
	'cachedContent' => ['name' => 'Cached Content', 'parameters' => []],
];
?>

<nav id="menuSub">
	<ul>
		<? foreach ($buttons as $action => $button): ?>
			<li>
				<?= $view->partial(
					'menuButton',
					[
						'url'        => $action,
						'name'       => $button['name'],
						'parameters' => $button['parameters'],
					],
					'Foomo\\Frontend'
				); ?>
			</li>
		<? endforeach; ?>
	</ul>
</nav>
