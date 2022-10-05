<?php
use app\components\Helpers;
use app\models\VehicleCoverageInput;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\VehicleCoverageInput */
/* @var $form yii\widgets\ActiveForm */
/** @var TYPE_NAME $productModel */
/** @var TYPE_NAME $curStock */
/** @var TYPE_NAME $paidNotShippedOrder */
/** @var TYPE_NAME $descriptionList */
/** @var TYPE_NAME $intransitETA */
?>

  <div class="vehicle-coverage-input-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-sm-4">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group input-sm">
              <?=$form->field($model, 'model_id')->dropDownList($productModel, ['class' => 'form-control select2 input-sm', 'disabled'=>'disabled'])?>
            </div>
          </div>
        </div>
        <div class="row m_top25">
          <div class="col-sm-12">
            <div class="form-group input-sm">
              <label for="curStock"><?=$descriptionList[VehicleCoverageInput::CURRENT_STOCK]?>: </label>
              <?=
              Html::input('number', 'curStock',
                $curStock,
                $options = [
                  'class' => 'form-control input-sm',
                  'id' => 'curStock'
                ])?>
            </div>
          </div>
        </div>
        <div class="row m_top25">
          <div class="col-sm-12">
            <div class="form-group input-sm">
              <label for="uamStock"><?=$descriptionList[VehicleCoverageInput::UAM_STOCK]?>: </label>
              <?=
              Html::input('number', 'uamStock',
                $uamStock,
                $options = [
                  'class' => 'form-control input-sm',
                  'id' => 'uamStock'
                ])?>
            </div>
          </div>
        </div>
        <div class="row m_top25">
          <div class="col-sm-12">
            <div class="form-group  input-sm">
              <label for="paidNotShippedOrder"><?=$descriptionList[VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER]?>: </label>
              <?=Html::input('number', 'paidNotShippedOrder',
                $paidNotShippedOrder,
                $options = [
                  'class' => 'form-control input-sm',
                  'id' => 'paidNotShippedOrder'
                ])?>
            </div>
          </div>
        </div>
        <div class="row m_top25">
          <div class="col-sm-12">
            <div class="form-group input-sm pull-right">
              <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
              <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-8">
        <fieldset class="scheduler-border">
          <legend class="scheduler-border">
            <?=$descriptionList[VehicleCoverageInput::INTRANSIT_ETA]?>
          </legend>
          <table class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
            <thead>
            <tr>
              <th class="text-center col-xs-1">
                <i id="addItem" class="fa fa-plus-square text-green font-weight-bold" style="font-size:150%"></i>
              </th>
              <th class="v_middle  col-xs-6"><?=Yii::t('app', 'Date')?></th>
              <th><?=Yii::t('app', 'Qty')?></th>
            </tr>
            </thead>
            <tbody id="list_ETA">
            <?
            if(count($intransitETA)>0){
              foreach ($intransitETA as $forDate => $qtyETA) {?>

                <tr id='<?=time()?>' class='v_urta'>
                  <td class='text-center'><i class='row_remove fa fa-remove font-weight-bold text-danger'></i></td>
                  <td>
                    <input type='date' id='etaDt<?=time()?>' name='eta[date][]' value='<?=$forDate?>' >
                  </td>
                  <td>
                    <input type='text' class='form-control item_qty' id='etaQty<?=time()?>' name=eta[qty][] value="<?=Helpers::numberFormatRemoveZero($qtyETA, 4, ".", "")?>">
                  </td>
                </tr>

              <?}
            }?>

            </tbody>
          </table>
        </fieldset>
      </div>
    </div>
<!--    --><?//=DateTimePicker::widget([
//      'name' => 'dp_1',
//      'value' => '2020-04-14',
//      'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
//      'layout' => '{picker}{input}{remove}',
//      'removeButton' => ['position' => 'append'],
//      'language' => 'ru',
//      'pluginOptions' => [
//        'autoclose' => true,
//        'format' => 'yyyy-mm-dd',
//        'startView' => 'month',
//        'minView' => 'month',
//        'maxView' => 'month',
//      ],
//      'options' => [
//        'autocomplete' => 'off',
//        'placeholder' => 'YYYY-MM-DD',
//        'class' => 'form-control input-sm'
//      ]
//    ]);
//    ?>
    <?php ActiveForm::end(); ?>

  </div>
<?
$add_item = <<< JS
$(document).ready(function() {
	
	$(document).on('click', '.row_remove', function(){
	  var tr_id = $(this).closest('tr').attr('id');
	  $(this).closest('tr').remove();
	});	
	$(document).on('click', '#addItem', function(){
	 var cur_time = new Date().getTime();
		 var append_row ="<tr id='"+cur_time+"' class='v_urta'>"+
			"<td class='text-center'><i class='row_remove fa fa-remove font-weight-bold text-danger'></i></td>"+
				"<td>" +
				  "<input type='date' id='etaDt"+cur_time+"' name='eta[date][]' value='' >"+
				  "</td>"+
				"<td><input type='text' class='form-control item_qty' id='etaQty"+cur_time+"' name=\"eta[qty][]\"/></td>"+				
			"</tr>";
		 $("#list_ETA").append(append_row);
	});
	
});
JS;
$this->registerJs($add_item, yii\web\View::POS_END);
?>