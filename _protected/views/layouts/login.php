<?php
	/* @var $this View */
	/* @var $content string */
	use app\assets\AppAsset;
	use app\widgets\Alert;
	use yii\helpers\Html;
	use yii\web\View;

	AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
	<meta charset="<?=Yii::$app->charset?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?=Html::csrfMetaTags()?>
	<title><?=Yii::t('app', Yii::$app->name)?> - <?=Html::encode($this->title)?></title>
	<link href='https://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
	<div class="container">
		<?=Alert::widget()?>
		<?=$content?>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; <?=Yii::t('app', Yii::$app->name)?> <?=date('Y')?></p>
		<p class="pull-right"><?=Yii::t('app', 'JSC GM UZBEKISTAN')?></p>
	</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
