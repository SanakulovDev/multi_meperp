<?php
use app\models\Contract;
use app\models\Customer;
use app\models\Factory;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $form yii\widgets\ActiveForm */
/* @var TYPE_NAME $errorlist */
/** @var TYPE_NAME $detail_count */
?>
<div class="fg-invoice-form">
  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?=$form->field($model, 'invoice_no')
              ->label(Yii::t('app', 'Waybill no'))
              ->textInput(['maxlength' => true, 'class' => 'form-control'])?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?=$form->field($model, 'invoice_date')
              ->label(Yii::t('app', 'Waybill date'))
              ->widget(DateTimePicker::classname(), [
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
                  'class' => 'form-control'
                ]
              ]);
      ?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      $factories = Factory::find()->all();
      $factory_items = ArrayHelper::map($factories, 'id', 'factoryinfo');
      $params = ['prompt' => '. . .', 'class' => 'form-control', 'disabled' => $detail_count ? true : false];
      echo $form->field($model, 'factory_id')->dropDownList($factory_items, $params);
      ?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      $customers = Customer::find()->all();
      $customer_items = ArrayHelper::map($customers, 'id', 'name');
      $params = ['prompt' => '. . .', 'class' => 'form-control select2'];
      echo $form->field($model, 'customer_id')->dropDownList($customer_items, $params);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      $contracts = Contract::find()->select(["id", "CONCAT(contract_no, '(', contract_date,')') as contract_no"])->all();
      $contract_items = ArrayHelper::map($contracts, 'contract_no', 'contract_no');
      $params = ['prompt' => '. . .', 'class' => 'form-control'];
      echo $form->field($model, 'contract')->textInput(['list' => "contract_list", 'maxlength' => true, 'class' => 'form-control']) ?>
      <datalist id="contract_list">
        <?=Html::dropDownList('contract_list', null, $contract_items, $params)?>
      </datalist>
    </div>
    <div class="col-md-2 col-sm-2 col-lg-2">
      <?=$form->field($model, 'vat')->textInput(['value' => Yii::$app->params['vat'], 'maxlength' => true, 'class' => 'form-control'])?>
    </div>

    <!--<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*
				$doveronnosts = ReceivingPerson::find()->select(['fullname', 'doc_number'])->all();
				$doveronnost_items = ArrayHelper::map($doveronnosts, 'fullname', 'doc_number');
			*/ ?>
			<datalist id="doveronnost_list">
				<? /*
					foreach($doveronnost_items as $key => $item){
						*/ ?>
						<option value="<? /*=$key*/ ?>"><? /*=$item*/ ?></option>
					<? /* }
				*/ ?>
			</datalist>
			<? /* echo $form->field($model, 'rec_person_fullname')->textInput(['id' => 'rec_person_fullname', 'list' => "doveronnost_list", 'maxlength' => true, 'class' => 'form-control']) */ ?>
		</div>
		<div class="col-md-3 col-sm-3 col-lg-3">
			<? /* echo $form->field($model, 'rec_person_regno')->textInput(['id' => 'rec_person_regno', 'maxlength' => true, 'class' => 'form-control']) */ ?>
		</div>

		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*
				$drivers = Driver::find()->select(["id", "CONCAT(first_name, ' ',last_name,' ',middle_name,'(',emp_no,')' ) as first_name"])->all();
				$driver_items = ArrayHelper::map($drivers, 'first_name', 'first_name');
				$params = ['prompt' => '. . .', 'class' => 'form-control'];
				echo $form->field($model, 'driver')->textInput(['list' => "driver_list", 'maxlength' => true, 'class' => 'form-control']) */ ?>
			<datalist id="driver_list">
				<? /*=Html::dropDownList('driver_list', null, $driver_items, $params)*/ ?>
			</datalist>

		</div>
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*
				$trucks = Truck::find()->select(["id", "CONCAT(model, '(', number,')') as model"])->all();
				$truck_items = ArrayHelper::map($trucks, 'model', 'model');
				$params = ['prompt' => '. . .', 'class' => 'form-control'];
				echo $form->field($model, 'truck')->textInput(['list' => "truck_list", 'maxlength' => true, 'class' => 'form-control']) */ ?>
			<datalist id="truck_list">
				<? /*=Html::dropDownList('truck_list', null, $truck_items, $params)*/ ?>
			</datalist>
		</div>-->

  </div>

  <!--<div class="row">
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*=$form->field($model, 'sender')->textInput(['maxlength' => true, 'class' => 'form-control'])*/ ?>
		</div>
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*=$form->field($model, 'excise')->textInput(['value' => Yii::$app->params['excise'], 'maxlength' => true, 'class' => 'form-control'])*/ ?>
		</div>
	</div>-->
  <div class="row">
    <div class="col-sm-12">
      <?=$form->field($model, 'comment')->textInput(['class' => 'form-control'])?>
    </div>
  </div>

  <div class="form-group pull-right">
    <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
    <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
  </div>
  <?php ActiveForm::end(); ?>
