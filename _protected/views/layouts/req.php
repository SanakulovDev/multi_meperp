<?php
	/* @var $this View */
	/* @var $content string */
	use app\assets\ReqAsset;
	use yii\helpers\Html;
	use yii\web\View;

	ReqAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
	<meta charset="<?=Yii::$app->charset?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<?=Html::csrfMetaTags()?>
	<title><?=Yii::$app->name?> - <?=Html::encode($this->title)?></title>
	<?php $this->head() ?>
	<base href="<?=Yii::getAlias('@themes')?>/">
</head>


<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<?=$content?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
