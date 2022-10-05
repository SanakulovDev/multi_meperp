<?php
	/* @var $this yii\web\View */
	/* @var $name string */
	/* @var $message string */
	/* @var $exception Exception */
	use yii\helpers\Html;

	$this->title = Yii::t('app', 'Error');
?>
<div class="site-error">
	<div class="error-content">
		<h2> <i class="fa fa-warning text-red f_bold"></i> Oops! Something went wrong. </h2>
		<p class="text-red">
				<?=nl2br(Html::encode($exception->getMessage()))?>
		</p>
	</div>
</div>
