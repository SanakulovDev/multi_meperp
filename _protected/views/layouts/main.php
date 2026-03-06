<?php
	/* @var $this View */
	/* @var $content string */
	use app\assets\AppAsset;
	use app\widgets\Alert;
	use yii\bootstrap\Modal;
	use yii\bootstrap\Nav;
	use yii\bootstrap\NavBar;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\web\View;
	use yii\widgets\Breadcrumbs;

	AppAsset::register($this);
	$faviconUrl = Url::home().'/img/Logo.svg';
	//		echo "<pre>1:"; print_r($faviconUrl);echo "</pre>";
	//		die;
	$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to([$faviconUrl])]);
	$js = <<< JS
    $(function  () {
      $(".form-modal").click(function(e){
          e.preventDefault();
          $("#modal").modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));             
      });
      
    });
JS;
	$this->registerJs($js);
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
	<?php
		NavBar::begin([
			              'brandLabel' => Yii::t('app', Yii::$app->name),
			              'brandUrl' => Yii::$app->homeUrl,
			              'options' => [
//											'class' => 'navbar-default navbar-fixed-top sidebar-mini sidebar-collapse',
'class' => 'navbar-default sidebar-mini sidebar-collapse',
			              ],
		              ]);
		// everyone can see Home page
		$menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']];
		// we do not need to display About and Contact pages to employee+ roles
		if(!Yii::$app->user->can('employee')){
			$menuItems[] = ['label' => Yii::t('app', 'About'), 'url' => ['/site/about']];
			$menuItems[] = ['label' => Yii::t('app', 'Contact'), 'url' => ['/site/contact']];
		}
		// display Users to admin+ roles
		if(Yii::$app->user->can('admin')){
			$menuItems[] = ['label' => Yii::t('app', 'Users'), 'url' => ['/user/index']];
		}
		// display Logout to logged in users
		if(!Yii::$app->user->isGuest){
			$menuItems[] = [
				'label' => Yii::t('app', 'Logout').' ('.Yii::$app->user->identity->username.')',
				'url' => ['/site/logout'],
				'linkOptions' => ['data-method' => 'post']
			];
		}
		// display Signup and Login pages to guests of the site
		if(Yii::$app->user->isGuest){
			$menuItems[] = ['label' => Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
			$menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
		}
		echo Nav::widget([
			                 'options' => ['class' => 'navbar-nav navbar-right'],
			                 'items' => $menuItems,
		                 ]);
		NavBar::end();
	?>
	<div class="container">
		<?=Breadcrumbs::widget([
			                       'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		                       ])?>
		<?=Alert::widget()?>
		<?php
			Modal::begin([
				             'options' => [
					             'tabindex' => false // important for Select2 to work properly
				             ],
				             'header' => '<h4></h4>',
				             'id' => 'modal',
				             'size' => 'modal-md'
			             ]);
			echo '<div id="modalContent"></div>';
			Modal::end();
		?>
		<?=$content?>
		<footer class="footer">
			<div class="container">
				<p class="pull-left">&copy; <?=Yii::t('app', Yii::$app->name)?> <?=date('Y')?></p>
				<p class="pull-right"><?=Yii::t('app', 'JSC GM UZBEKISTAN')?></p>
			</div>
		</footer>
	</div>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
