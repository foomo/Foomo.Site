<?php
/* @var $model \Foomo\Site\Mail\Frontend\Model */
/* @var $view \Foomo\Site\Mail\Frontend\View */
# render content first so it can set/switch the layout
$content = $view->partial($view->getContentPartial());
echo $view->partial($view->getLayoutPartial(), compact('content'));
