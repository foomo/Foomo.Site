<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */

$action = $view->currentAction;
$dimension = $model->dimension;
?>

<div class="appContent">
  <div class="rightBox">
    Dimension: <select
      style="width:100px;margin-right:10px;"
      onchange="window.location.href=$(this).val();return false;"
      >
      <? foreach ($model->repoNodes as $dimension => $repoNode): ?>
				<option
					value="<?= $view->url($view->currentAction, [$dimension]) ?>"
					<?= ($dimension == $model->dimension) ? ' selected="selected"' : '' ?>
					>
					<?= $dimension; ?>
				</option>
      <? endforeach; ?>
    </select>

    <a
      class="linkButtonYellow"
      href="<?= $view->url('updateServer', compact('action', 'dimension')) ?>"
      >
      <span class="fa fa-refresh"></span> Update Repository
    </a>
  </div>
</div>
