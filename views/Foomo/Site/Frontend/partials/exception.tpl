<?php
/* @var $view \Foomo\MVC\View */
/* @var $exception \Exception */
?>

<div class="cms-content container-fluid">
	<div class="row">
		<div class="col-md-6 col-sm-12">

			<div class="error-page">

				<?php
					if ($exception instanceof \Foomo\Site\Exception\Content) {
						echo $view->partial("exception/" . $exception->getCode());
					} else {
						echo $view->partial("exception/500");
					}
				?>

			</div>

		</div>
	</div>
</div>