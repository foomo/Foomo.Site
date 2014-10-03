<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
?>

<?= $view->partial('menu'); ?>

<div id="appContent">

	<?= $view->partial('controls'); ?>

	<h2>Content Server Repository</h2>

	<table class="pure-table" style="width:100%;margin:20px 0;">
		<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Handler</th>
			<th>Url</th>
		</tr>
		</thead>
		<tbody>
		<?= $view->partial(
			'repoNode',
			[
				'level'      => 0,
				'parentNode' => null,
				'repoNode'   => $model->getRepoNode(),
			]
		); ?>
		</tbody>
	</table>
</div>
