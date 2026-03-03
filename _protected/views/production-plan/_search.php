<?php
use app\models\Part;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPlanSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class=" production-plan-search" data-intro="<?=Yii::t('intro', 'Active_form_search')?>">
  <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get', 'options' => ['data-pjax' => 1],]); ?>
	<div class="row">
		<div class="col-md-2">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'shift')?>
				<span><?=Yii::t('app', 'Shift')?></span>
			</label>
		</div>
		<div class="col-md-3">
			<label class="form-group has-float-label">
        <?
        $parts = Part::find()->all();
        $items = ArrayHelper::map($parts, 'id', 'part_no');
        $params = ['prompt' => '. . .', 'class' => 'form-control select2'];
        echo $form->field($model, 'part_id')->dropDownList($items, $params);
        ?>
				<span><?=Yii::t('app', 'Part No')?></span>
			</label>
		</div>
		<div class="col-md-2">
			<label class="form-group has-float-label">
        <?php echo $form->field($model, 'target_qty') ?>
				<span><?=Yii::t('app', 'Target qty')?></span>
			</label>
		</div>
		<div class="col-md-2">
      <?
      $cond = (!Yii::$app->user->can('admin')) ? ['and', ['in', 'id', Yii::$app->user->identity->warehouseIds], ['warehouse_type' => [0, 1]]] : ['warehouse_type' => [0, 1]];
      $params = ['prompt' => '. . .', 'class' => 'form-control select2'];
      ?>
			<label class="form-group has-float-label">
        <?=$form->field($model, 'warehouse_id')
                ->dropDownList(ArrayHelper::map(Warehouse::find()->where($cond)->all(), 'id', 'name'), $params);?>
				<span><?=Yii::t('app', 'Location')?></span>
			</label>
		</div>
		<div class="col-md-3">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'production_date')->widget(DateTimePicker::classname(), [
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
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ]);?>
				<span><?=Yii::t('app', 'Production date')?></span>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="pull-right">
        <?=Html::resetButton(Yii::t('app', 'btn-cancel'), ['class' => 'btn btn-default btn-sm'])?>
        <?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-sm'])?>
			</div>
		</div>
	</div>
  <?php ActiveForm::end(); ?>
</div>
