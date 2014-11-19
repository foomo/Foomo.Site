<?php
/* @var $view \Foomo\Site\Toolbox\Adapter\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\Adapter\Frontend\Model */
$buttons = [
	'deleteCachedContent' => [
		'name'       => 'Delete all',
		'icon'       => 'close',
		'parameters' => [],
	]
];
?>

<?= $view->partial('menu'); ?>

<div id="appContent">
	<? if (!empty($model->getCachedContent())): ?>
		<?= $view->partial('controls', compact('buttons')); ?>

		<? foreach ($model->getCachedContent() as $clientClass => $resources): ?>

			<h2>Cached Content for: <i><?= $clientClass ?></i></h2>

			<table class="pure-table" style="width:100%;margin:20px 0;">
				<thead>
				<tr>
					<th>Dimension</th>
					<th>ID</th>
					<th>Date</th>
					<th>Time</th>
					<th>Hits</th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<? /* @var $resource \Foomo\Cache\CacheResource */ ?>
				<? foreach ($resources as $resource): ?>
					<? $nodeId = $resource->properties['nodeId'];; ?>
					<? $dimension = $resource->properties['dimension']; ?>
					<tr>
						<td><?= $dimension; ?></td>
						<td><?= $nodeId; ?></td>
						<td><?= date('d.m.Y', $resource->creationTime); ?></td>
						<td><?= date('H:i:s', $resource->creationTime); ?></td>
						<td><?= $resource->hits; ?></td>
						<td>
							<a
								title="Delete"
								style="text-decoration:none;color:red;"
								onclick="return confirm('Delete content <?= $nodeId; ?> in dimension <?= $dimension ?>?');"
								href="<?= $view->url('deleteCachedContent', compact('nodeId', 'dimension')) ?>"
								><span class="fa fa-file-o"></span></a>
						</td>
						<td>
							<a
								title="Delete in all dimensions"
								style="text-decoration:none;color:red;"
								onclick="return confirm('Delete content <?= $nodeId; ?> in all dimensions?');"
								href="<?= $view->url('deleteCachedContent', compact('nodeId')) ?>"
								><span class="fa fa-files-o"></span></a>
						</td>
					</tr>
				<? endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="7"><?= count($resources) ?> records found</th>
					</tr>
				</tfoot>
			</table>
		<? endforeach; ?>
	<? else: ?>
		<h1>No cache resources!</h1>
	<? endif; ?>

</div>
