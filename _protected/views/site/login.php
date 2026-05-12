<?php
	/* @var $this yii\web\View */
	/* @var $form yii\bootstrap\ActiveForm */
	/* @var $model LoginForm */
	use app\models\LoginForm;
	use app\widgets\Alert;
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;

	$this->title = Yii::t('app', 'Login');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-lg-4 col-lg-offset-4" style="margin-top: 20px;">

		<div class="login-logo" style="margin-bottom: 10px;">
			<a href="<?=Yii::$app->homeUrl?>">
				<img src="/img/Logo.svg" style="width:50%"/>
			</a>
			<h4 class="text-center" style="color: #444444;margin-top: 20px;">E R P</h4>
		</div>


		<div class="login-box" style="width: 90%;margin: 0px auto; border: 1px solid #cccccc;">


			<!-- /.login-logo -->
			<div class="login-box-body">
				<?=Alert::widget()?>
				<p class="login-box-msg" style="font-size: 20px;"><?=Yii::t('app', 'Authorization on the system')?></p>

				<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

				<?=$form->field($model, 'username', [
					'template' => '<div class="form-group has-feedback">{input}<span class="glyphicon glyphicon-user form-control-feedback"></span>{hint}{error}</div>'
				])
				        ->textInput(['placeholder' => Yii::t('app', 'Username'), 'autofocus' => true])->label(false)
				?>

				<?=$form->field($model, 'password', [
					'template' => '<div class="form-group has-feedback">{input}<span class="glyphicon glyphicon-lock form-control-feedback"></span>{hint}{error}</div>'
				])
				        ->passwordInput(['placeholder' => Yii::t('app', 'Password')])->label(false)
				?>


				<div class="row">

					<div class="col-xs-8">
						<?=$form->field($model, 'rememberMe', [
							'options' => [
								'tag' => 'div',
								'class' => 'checkbox icheck'
							],
							'template' => 'div class="checkbox icheck">{input}</div>',
						])->checkbox()?>

					</div>

					<!-- /.col -->
					<div class="col-xs-4">
						<?=Html::submitButton(Yii::t('app', 'btn-login'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button'])?>
					</div>

					<!-- /.col -->
				</div>
				<?php ActiveForm::end(); ?>


			</div>
			<!-- /.login-box-body -->
		</div>

	</div>
</div>

<style>
	.icheck .checkbox label{
		padding-left:0px !important;
	}
</style>
