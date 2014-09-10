<?php
/* @var $model \Foomo\Site\Frontend\Model */
/* @var $view \Foomo\MVC\View */
?>

<div class="page">
	<div class="row-offcanvas row-offcanvas-left">
		<div class="container-offcanvas full-height">
			<div class="overflow-visible">
				<div class="col-xs-12 body">
					<pre>HEADER</pre>
					<pre>SUBNAVIGATION</pre>
					<pre>CONTENT</pre>
					<?php //echo ($content) ? $content : $view->partial($model->getSiteContent()->handler) ?>
					<pre>FOOTER</pre>
				</div>
			</div>
			<?php echo $view->partial('navigation/mobile') ?>
		</div>
	</div>
</div>

<?php echo $view->partial('tracking/tracking'); ?>