<?php
use app\enums\CargoType;
use app\enums\ContainerType;
use app\models\ContainerInvoice;
use app\models\Currency;
use app\models\DeliveryTerm;
use app\models\ShipMode;
use app\models\Supplier;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContainerInvoice */
/* @var $form yii\widgets\ActiveForm */
/* @var TYPE_NAME $errorlist */
/* @var TYPE_NAME $modelContainer */
/* @var TYPE_NAME $modelInvoice */
/* @var TYPE_NAME $modelItems */
?>

<div class="invoice-conttainer-form">
  <?php $form = ActiveForm::begin(); ?>
	<div class="row">
    <? if($model->isNewRecord) { ?>
			<div class="col-md-2">
        <?=$form->field($model, 'invoice_no')->textInput(['maxlength' => true, "value" => $partOrder->order_no])
                ->label(Yii::t('app', 'Invoice no'))
        ?>
			</div>
    <? } ?>
		<div class="col-md-2">
      <?
      $data = Supplier::find()->orderBy(['name' => SORT_ASC])->all();
      $items = ArrayHelper::map($data, 'id', 'name');
      $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => $contract->supplier_id];
      echo $form->field($model, 'supplier')->dropDownList($items, $params)
                ->label(Yii::t('app', 'Supplier'));
      ?>
		</div>
		<div class="col-md-2">
      <?=$form->field($model, 'ship_mode_id')
              ->label(Yii::t('app', 'Ship mode'))
              ->dropDownList(ArrayHelper::map(ShipMode::find()->all(), 'id', 'name'), [
                "value" => !$model->isNewRecord ? $model->ship_mode_id : 3
              ])
      ?>
		</div>
    <? if(!$model->isNewRecord) { ?>
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Container type')?></label>
          <?=Html::dropDownList("Container[container_type]", "$modelContainer->container_type", ContainerType::list(), ['class' => 'form-control cont-type-val cont-type-required'])?>
				</div>
			</div>
    <? } ?>
		<div class="col-md-2">
      <?
      $data = DeliveryTerm::find()->orderBy(['name' => SORT_ASC])->all();
      $items = ArrayHelper::map($data, 'id', 'name');
      $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => !$model->isNewRecord ? $model->delivery_term_id : 9];
      echo $form->field($model, 'delivery_term_id')->dropDownList($items, $params);
      ?>
		</div>
		<div class="col-md-2">
      <?
      $data = Currency::find()->orderBy(['code' => SORT_ASC])->all();
      $items = ArrayHelper::map($data, 'id', 'code');
      $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => !$model->isNewRecord ? $model->currency : 1];
      echo $form->field($model, 'currency')->dropDownList($items, $params)
                ->label(Yii::t('app', 'Currency'));
      ?>
		</div>
	</div>

  <? if(!$model->isNewRecord) { ?>
		<div class="row">
			<div class="col-lg-2">
        <?=$form->field($model, 'net_weight')->textInput(['maxlength' => true, "value" => !$model->isNewRecord ? intval($model->net_weight) : 1000 ])?>
			</div>
			<div class="col-lg-2">
        <?=$form->field($model, 'gross_weight')->textInput(['maxlength' => true, "value" => !$model->isNewRecord ? intval($model->gross_weight) : 1 ])?>
			</div>
			<!-- <div class="col-lg-2">
        <?=$form->field($model, 'cbm')->textInput(['maxlength' => true])?>
			</div> -->
			<div class="col-lg-2">
        <?=$form->field($model, 'cargo_type')->dropDownList(CargoType::list(), ['prompt' => '...', "value" => 2])?>
			</div>
		</div>
  <? } ?>

  <? if($model->isNewRecord) {
    echo $this->render('__container-shipdt',
      [
        'errorlist' => $errorlist ?? null,
        'items' => $items,
        'model' => $model,
        'modelContainer' => $modelContainer,
        'modelInvoice' => $modelInvoice,
        'modelItems' => $modelItems,
      ]);
  } else { ?>
		<div class="row">
			<div class="col-lg-3">
        <?=$form->field($model, 'shipped_at')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->shipped_at,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ])->label(Yii::t('app', 'Shipped at'));
        ?>
			</div>
			<div class="col-lg-3">
        <?=$form->field($model, 'need_at')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->need_at,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ]);
        ?>
			</div>
			<!-- <div class="col-lg-3">
        <?=$form->field($model, 'app_arr_at')->widget(DateTimePicker::classname(), [
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
        ]);
        ?>
			</div> -->

			<div class="col-lg-3">
        <?=$form->field($model, 'station_date')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->station_date,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ]);
        ?>
			</div>


		</div>
		<div class="row">

			<div class="col-lg-3">
        <?=$form->field($model, 'arrived_at')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->arrived_at,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ])->label(Yii::t('app', 'Arrived at'));
        ?>
			</div>

			<div class="col-lg-3">
        <?=$form->field($model, 'current_locate')->textInput(['maxlength' => true])
                ->label(Yii::t('app', 'Current location'))?>
			</div>
			<div class="col-lg-3">
        <?=$form->field($model, 'current_at')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->current_at,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ]);
        ?>
			</div>
		</div>
		<div class="row">


		</div>
		<div class="row">

			<div class="col-lg-3">
        <?
        $params = ['prompt' => '. . .', null, 'class' => 'form-control select2', "value" => 40];
        echo $form->field($model, 'regime')->dropDownList(ContainerInvoice::$regimeList, $params)
                  ->label(Yii::t('app', 'Customs regime'));
        ?>
			</div>

			<div class="col-lg-3">
        <?=$form->field($model, 'passed_at')->widget(DateTimePicker::classname(), [
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
            'value' => !$update ? date('Y-m-d') : $model->passed_at,
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => ' form-control'
          ]
        ])->label(Yii::t('app', 'Passed at'));
        ?>
			</div>

		</div>
  <? } ?>

	<div class="form-group pull-right">
    <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
    <?=Html::submitButton(Yii::t('app', 'Далее'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

  <?php ActiveForm::end(); ?>

</div>

<?php
$icheck_script = <<< JS
$(document).ready(function() {  
  changeContTypeVal();
  $("#containerinvoice-ship_mode_id").change(function() {
      changeContTypeVal();
  });
  $("#btnAddContDetail").click(function() {
      changeContTypeVal();
  });  
  function changeContTypeVal(){
    let shipModeVal = $("#containerinvoice-ship_mode_id").val();
    if (shipModeVal == 2) {
      $(".cont-type-val").attr("disabled",false);      
      $(".cont-type-required").attr("required",true);
      $(".cont-type-required").parent().addClass("required");
    }else {
      $(".cont-type-val").attr("disabled",true);      
      $(".cont-type-required").attr("required",false);
      $(".cont-type-required").parent().removeClass("required");
      $(".cont-type-val").val(0); 
    }
  }
});
JS;
$this->registerJs($icheck_script);
?>
