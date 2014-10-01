<?php
/* @var $view \Foomo\Site\Toolbox\ContentServer\Frontend\View */
/* @var $model \Foomo\Site\Toolbox\ContentServer\Frontend\Model */
?>

<?= $view->partial('menu'); ?>

<div id="appContent">
  <?= $view->partial('controls'); ?>

  <h2>JSON Content Server Repository</h2>

  <div class="greyBox">
    <?

    $geshi = new GeSHi(json_encode($model->getRepoNode(), JSON_PRETTY_PRINT), 'Javascript');
    echo $geshi->parse_code();
    ?>
  </div>
</div>
