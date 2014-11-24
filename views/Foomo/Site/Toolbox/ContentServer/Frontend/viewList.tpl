<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
?>

<?= $view->partial('menu'); ?>

<div id="appContent">

	<?= $view->partial('controls'); ?>

	<h2>Content Server Repository</h2>

	<table class="pure-table" style="width:100%;margin:20px 0;">
		<colgroup>
			<col width="40"/>
			<col/>
			<col/>
			<col width="40"/>
			<col width="40"/>
			<col width="40"/>
			<col width="40"/>
		</colgroup>
		<thead>
			<tr>
				<th style="min-width:15px">ID</th>
				<th>Name</th>
				<th>Url</th>
				<th colspan="2">Link</th>
				<th colspan="2">Cache</th>
			</tr>
		</thead>
		<tbody>
			<?= $view->partial(
				'repoNode',
				[
					'level'      => 0,
					'parentNode' => null,
					'repoNode'   => $model->repoNode,
				]
			); ?>
		</tbody>
	</table>
</div>
