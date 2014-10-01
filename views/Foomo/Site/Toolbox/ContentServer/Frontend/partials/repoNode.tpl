<?php
/* @var $view \Foomo\MVC\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
/* @var $repoNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $parentNode \Foomo\ContentServer\Vo\Content\RepoNode */
/* @var $level int */

$styles = [];
$region = $model->region;
$language = $model->language;

# check visibility
if (
	isset($repoNode->hidden->$region) &&
	isset($repoNode->hidden->$region->$language) &&
	$repoNode->hidden->$region->$language
) {
	$styles[] = 'opacity:0.5';
}

# indent
$mimeTypes = [
	'application/neos+page'      => 'fa-file-o',
	'application/neos+shortcut'  => 'fa-link',
	'application/neos+external'  => 'fa-external-link',
	'application/neos+directory' => 'fa-folder-o',
	'application/shop+category'  => 'fa-shopping-cart',
];
?>

<tr style="<?= implode(';', $styles); ?>">
	<td>
		<a
			href="#"
			style="text-decoration:none"
			title="<?= $repoNode->mimeType ?>"
			class="fa <?= (isset($mimeTypes[$repoNode->mimeType])) ? $mimeTypes[$repoNode->mimeType] : 'fa-question' ?>"
			onclick="alert('<?= $repoNode->id ?>');return false;"
			></a>
	</td>
	<td style="padding-left:<?= (1 + $level); ?>em;">
		<?= $repoNode->names->$region->$language ?>
	</td>
	<td><?= $repoNode->handler; ?></td>
	<td>
		<a href="<?= $repoNode->URIs->$region->$language ?>" target="_blank">
			<?= $repoNode->URIs->$region->$language ?>
		</a>
	</td>
	<td>
		<? if ($model->isNeosCacheable($repoNode)): $cached = $model->isCached($repoNode); ?>
			<span
				style="color:<?= (!$cached) ? '#e74c3c' : '#27ae60'; ?>"
				title="<?= (!$cached) ? 'Not cached' : 'Cached on: ' . date('d.m.Y H:i:s', $cached) ?>"
				class="fa fa-save"
				></span>
		<? endif; ?>
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
