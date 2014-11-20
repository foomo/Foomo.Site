<?php
/* @var $view \Foomo\MVC\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
/* @var $repoNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $parentNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $level int */

$styles = [];
$nodeId = $repoNode->id;
$dimension = $model->dimension;
$action = $view->currentAction;

# check visibility
if ($repoNode->hidden) {
	$styles[] = 'opacity:0.5';
}

# indent
$mimeTypes = [
	'application/foomo+app'        => 'fa-puzzle-piece',
	'application/neos+page'        => 'fa-file-text-o',
	'application/neos+shortcut'    => 'fa-link',
	'application/neos+external'    => 'fa-external-link',
	'application/neos+directory'   => 'fa-folder-o',
	'application/shop+category'    => 'fa-shopping-cart',
	'application/shop+placeholder' => 'fa-external-link-square',
	'application/shop+product'		 => 'fa-female',
];
?>

<tr style="<?= implode(';', $styles); ?>">
	<td style="text-align:center;">
		<a
			href="#"
			style="text-decoration:none"
			title="<?= $repoNode->mimeType ?>"
			class="fa <?= (isset($mimeTypes[$repoNode->mimeType])) ? $mimeTypes[$repoNode->mimeType] : 'fa-file-o' ?>"
			onclick="alert('NodeId:\n<?= $nodeId ?>');return false;"
			></a>
	</td>
	<td style="padding-left:<?= (1 + $level); ?>em;">
		<?= $repoNode->name ?>
	</td>
	<td>
		<a href="<?= $repoNode->URI ?>" target="_blank">
			<?= $repoNode->URI ?>
		</a>
	</td>
	<td style="text-align:center;">
		<? if (isset($repoNode->linkId) && $repoNode->linkId): ?>
			<a
				href="#"
				class="fa fa-link"
				style="text-decoration:none;"
				title="LinkId: <?= $repoNode->linkId ?>"
				onclick="alert('LinkId:\n<?= $repoNode->linkId ?>');return false;"
				></a>
		<? endif ?>
	</td>
	<td style="text-align:center;">
		<? if (isset($repoNode->destinationId) && $repoNode->destinationId): ?>
			<a
				href="#"
				class="fa fa-files-o"
				style="text-decoration:none;"
				title="DesinationId: <?= $repoNode->destinationId ?>"
				onclick="alert('DestinationId:\n<?= $repoNode->destinationId ?>');return false;"
				></a>
		<? endif ?>
	</td>
	<td style="text-align:center;">
		<? if (null != $resource = $view->getCachedContent($nodeId, $dimension)): ?>
			<? $all = false; ?>
			<a
				class="fa fa-file-o"
				style="text-decoration:none;color:darkred;"
				title="Delete cached resource"
				onclick="return confirm('Delete cache for <?= $repoNode->name; ?>?');"
				href="<?= $view->url('deleteCachedContent', compact('action', 'dimension', 'nodeId', 'all')) ?>"
				></a>
		<? endif ?>
	</td>
	<td style="text-align:center;">
		<? if (null != $resources = $view->getCachedContent($nodeId)): ?>
			<? $all = true; ?>
			<a
				class="fa fa-files-o"
				style="text-decoration:none;color:darkred;"
				title="Delete cached resource in all dimensions"
				onclick="return confirm('Delete cache in all dimensions for <?= $repoNode->name; ?>?');"
				href="<?= $view->url('deleteCachedContent', compact('action', 'dimension', 'nodeId', 'all')) ?>"
				></a>
		<? endif ?>
	</td>
	<?
	//and now some recursion for all the child nodes
	if (count($repoNode->index) > 0) {
		$level++;
		foreach ($repoNode->index as $index) {
			echo $view->partial(
				'repoNode',
				[
					'level'      => $level,
					'parentNode' => $repoNode,
					'repoNode'   => $repoNode->nodes->$index,
				]
			);
		}
	}
	?>
</tr>
