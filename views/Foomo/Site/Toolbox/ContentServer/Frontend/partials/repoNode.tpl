<?php
/* @var $view \Foomo\MVC\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
/* @var $repoNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $parentNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $level int */

$styles = [];

# check visibility
if ($repoNode->hidden) {
	$styles[] = 'opacity:0.5';
}

# indent
$mimeTypes = [
	'application/neos+page'        => 'fa-file-text-o',
	'application/neos+shortcut'    => 'fa-link',
	'application/neos+external'    => 'fa-external-link',
	'application/neos+directory'   => 'fa-folder-o',
	'application/shop+category'    => 'fa-shopping-cart',
	'application/shop+placeholder' => 'fa-close',
];
?>

<tr style="<?= implode(';', $styles); ?>">
	<td style="text-align:center;">
		<a
			href="#"
			style="text-decoration:none"
			title="<?= $repoNode->mimeType ?>"
			class="fa <?= (isset($mimeTypes[$repoNode->mimeType])) ? $mimeTypes[$repoNode->mimeType] : 'fa-file-o' ?>"
			onclick="alert('NodeId:\n<?= $repoNode->id ?>');return false;"
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
				style="text-decoration:none"
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
				style="text-decoration:none"
				title="DesinationId: <?= $repoNode->destinationId ?>"
				onclick="alert('DestinationId:\n<?= $repoNode->destinationId ?>');return false;"
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
