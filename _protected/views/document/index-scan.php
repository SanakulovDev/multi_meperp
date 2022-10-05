<?php
  
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\DocumentSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Part movements');
	$this->params['breadcrumbs'][] = $this->title;
?>


<div class="document-index">


	<div class="downtime-search">


		<div class="row">
			<div class="col-md-12 pull-left">
				<div class="form-group">
					<? if(Yii::$app->user->can('mrp')){ ?>
						<?=Html::a(Yii::t('app', 'btn-receipt'), ['receipt'], ['class' => 'btn btn-info btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-issue'), ['issue'], ['class' => 'btn btn-success  btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-consignment-issue'), ['consignment-issue'], ['class' => 'btn btn-warning  btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-consignment-receipt'), ['consignment-receipt'], ['class' => 'btn btn-info  btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-local-receipt'), ['local-receipt'], ['class' => 'btn btn-primary  btn-sm'])?>
					<? } ?>
					<? if(Yii::$app->user->can('mfu') or (Yii::$app->user->can('mrp') and Yii::$app->user->identity->act_access == 1)){ ?>
						<?=Html::a(Yii::t('app', 'btn-create-act'), ['create-act'], ['class' => 'btn btn-danger  btn-sm'])?>
					<? } ?>
				</div>
				<div class="form-group">
					<? if(Yii::$app->user->can('counter')){ ?>
						<?//=Html::a(Yii::t('app', 'btn-create-shop-consumption'), ['create-shop-consumption'], ['class' => 'btn btn-success  btn-sm'])?>
						<?//=Html::a(Yii::t('app', 'btn-shop-confirm'), ['shop-confirm'], ['class' => 'btn btn-info  btn-sm'])?>
<!--						&nbsp;&nbsp;&nbsp;&nbsp;-->
						<?=Html::a(Yii::t('app', 'btn-create-shop-consumption'), ['create-shop-consumption-ver2'], ['class' => 'btn btn-success  btn-sm'])?>
						<?=Html::a(Yii::t('app', 'btn-shop-confirm'), ['shop-confirm-ver2'], ['class' => 'btn btn-info  btn-sm'])?>
					<? } ?>
				</div>
			</div>
		</div>
    
    
    
		<div class="row">
			<?php
				$form = ActiveForm::begin([
					                          'action' => ['index'],
					                          'method' => 'get',
				                          ]);
			?>

			<div class="col-md-3">
				<?=$form->field($searchModel, 'docnum')->textInput(['placeholder' => $searchModel->getAttributeLabel('docnum'), 'class' => ' form-control input-sm'])->label(false)?>
			</div>
			<div class="col-md-3">

				<?=
					$form->field($searchModel, 'filter_from')->widget(DateTimePicker::classname(), [
						'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
						'layout' => '{picker}{input}{remove}',
						'removeButton' => ['position' => 'append'],
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd',
							'startView' => 'month',
							'minView' => 'month',
							'maxView' => 'month',
						],
						'options' => [
							'autocomplete' => 'off',
							'placeholder' => 'С...',
							'class' => ' form-control input-sm'
						]
					])
					     ->label(false)
				?>
			</div>
			<div class="col-md-3">
				<?=
					$form->field($searchModel, 'filter_to')->widget(DateTimePicker::classname(), [
						'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
						'layout' => '{picker}{input}{remove}',
						'removeButton' => ['position' => 'append'],
						'language' => 'ru',
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd',
							'startView' => 'month',
							'minView' => 'month',
							'maxView' => 'month',
						],
						'options' => [
							'autocomplete' => 'off',
							'placeholder' => 'До...',
							'class' => ' form-control input-sm'
						]
					])
					     ->label(false)
				?>
			</div>
			<?=$form->field($searchModel, 'document_type_id')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'from_warehouse_id')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'to_warehouse_id')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'series')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'status')->hiddenInput()->label(false)?>
			<?=$form->field($searchModel, 'serial_number')->hiddenInput()->label(false)?>
			<div class="col-md-3">
				<div class="form-group">
					<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
					<?=Html::a(Yii::t('app', 'btn-download'), ['xls', 'DocumentSearch' => ($_GET['DocumentSearch'] ?? null)], ['class' => 'btn btn-info btn-sm']);?>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>

	</div>


<?=$this->render('_index-list', [
		'DocumentSearch' => $searchModel,
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
	])?>


</div>
