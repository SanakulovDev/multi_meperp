<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

$baseUrl = Url::base(true);
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Neotherm ERP Print Pechat</title>
		<link rel="icon" type="image/ico" href="/img/Logo.svg" />
		<style type="text/css">
			.swal2-container.swal2-center .swal2-title img {
				border: 3px solid #e77713;
				width: 80px;
				border-radius: 50%;
				position: absolute;
				background-color: #fff;
				top: -44px;
				left: calc(50% - 40px);
			}
			.swal2-popup.swal2-modal.swal2-show {
				width: 415px !important;
			}
		</style>
	</head>
	<body>
		<?php $this->beginBody() ?>
			<?= $content ?>
		<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>
