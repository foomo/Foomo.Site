<?php
/* @var $view \Foomo\Site\Toolbox\Adapter\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\Adapter\Frontend\Model */
/* @var $buttons array */
?>

<div class="appContent">
  <div class="rightBox">
    <? foreach ($buttons as $action => $button): ?>
      <a class="linkButtonYellow" href="<?= $view->url($action, $button['parameters']); ?>">
        <span class="fa fa-<?= $button['icon']; ?>"></span> <?= $button['name']; ?>
      </a>
    <? endforeach; ?>
  </div>
</div>
