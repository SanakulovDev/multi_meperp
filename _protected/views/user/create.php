<?php
	/* @var $this yii\web\View */
	/* @var $user app\models\User */
	$this->title = Yii::t('app', 'Create User');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">


	<div class="col-md-8 well bs-component">

		<?=$this->render('_form', ['user' => $user])?>

	</div>

</div>

