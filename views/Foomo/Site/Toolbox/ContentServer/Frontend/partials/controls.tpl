<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
?>

<div class="appContent">
  <div class="rightBox">
    <select
      style="width:100px;margin-right:10px;"
      onchange="window.location.href=$(this).val();return false;"
      >
      <option value=""><?= $model->language . '_' . strtoupper($model->region); ?></option>
      <? foreach (\Foomo\Site::getConfig()->locales as $region => $languages): ?>
        <? foreach ($languages as $language): ?>
          <? if ($language == $model->language && $region == $model->region) { continue; } ?>
          <option value="<?= $view->url('default', [$region, $language]) ?>">
            <?= $language . '_' . strtoupper($region); ?>
          </option>
        <? endforeach; ?>
      <? endforeach; ?>
    </select>
    <a
      class="linkButtonYellow"
      href="<?= $view->url('update', array($model->region, $model->language)) ?>"
      >
      <span class="fa fa-refresh"></span> Update
    </a>
  </div>
</div>