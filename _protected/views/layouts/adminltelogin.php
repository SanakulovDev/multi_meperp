<?php
	/* @var $this View */
	/* @var $content string */
	use app\assets\AdminLteLoginAsset;
	use yii\helpers\Html;
	use yii\web\View;

	AdminLteLoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
	<meta charset="<?=Yii::$app->charset?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" href="<?=Yii::$app->homeUrl?>img/cropped-fav-32x32.png" sizes="32x32">
	<?=Html::csrfMetaTags()?>
	<title><?=Yii::$app->name?> - <?=Html::encode($this->title)?></title>
	<?php $this->head() ?>
	<!-- Google Font -->
	<!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
	<base href="<?=Yii::getAlias('@themes')?>/">
</head>


<body class="hold-transition login-page" style="background: #ffffff;">
<?php $this->beginBody() ?>

<?=$content?>
<?php
	$script = <<< JS
    $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
JS;
	$this->registerJs($script, yii\web\View::POS_END);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
