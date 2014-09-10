<?php
/* @var $model \Foomo\Site\Frontend\Model */
/* @var $view \Foomo\MVC\View */

$debug = (!\Foomo\Config::isProductionMode());

# get html doc
$HTMLDoc = \Foomo\HTMLDocument::getInstance();

# compile lte ie8 bundle
$lteie8 = \Foomo\Bundle\Compiler::compileAndCache('Foomo\\Site\\Bundles::lteie8Scripts', [$debug], $debug);


$HTMLDoc->addMeta([
	'viewport' => 'width=device-width,initial-scale=1'
]);

$HTMLDoc->addHead('
<!--[if lte IE 8]>
<script language="JavaScript" src="'.$lteie8->resources[0]->link.'" type="text/javascript"></script>
<![endif]-->
');


//$HTMLDoc->addJavascripts([
//	\Foomo\Media\Image\Adaptive::getStreamingJSCookieSnippet()
//]);

# add bundles
\Foomo\Bundle\Compiler::addBundleToDoc('Foomo\\Site\\Bundles::siteStyles', [$debug], $HTMLDoc, $debug);
\Foomo\Bundle\Compiler::addBundleToDoc('Foomo\\Site\\Bundles::siteScripts', [$debug], $HTMLDoc, $debug);

?>

<?php echo $view->partial('navbar'); ?>

<div class="container">
	<?php echo ($content) ? $content : $view->partial($model->getSiteContent()->handler) ?>
</div>

<?php echo $view->partial('footer'); ?>