<?php

use app\models\ConveyorType;
use app\models\LocationType;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

if (isset($location_type_id)) {
      $location_type_id = $location_type_id;
} else {
      $location_type_id = 0;
}

if (isset($model->id)) {
      $id = $model->id;
} else {
      $id = 0;
}

$validationUrl = ['validate'];
if (!$model->isNewRecord) {
      $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
      'id' => $model->formName(),
      'enableAjaxValidation' => true,
      'validateOnType' => false,
      'validationUrl' => $validationUrl,
      'options' => ['data-pjax' => true, 'class' => 'modalForm']
]);
?>

<div class="row">
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?
            $locationtype = LocationType::find()->all();
            $locationtype_items = ArrayHelper::map($locationtype, 'id', 'name');
            if ($location_type_id == 0) {
                  $params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2'];
                  echo $form->field($model, 'location_type_id')->dropDownList($locationtype_items, $params);
            } else {
                  $params = ['prompt' => '. . .', 'class' => 'form-control input-sm'];
                  echo $form->field($model, 'location_type_id')->dropDownList($locationtype_items, $params);
            }
            ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
      </div>
</div>
<div class="row">
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'is_main')->dropDownList($model->mainList) ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?
            $conveyortype = ConveyorType::find()->all();
            $conveyortype_items = ArrayHelper::map($conveyortype, 'id', 'name');
            $params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2'];
            echo $form->field($model, 'conveyor_type_id')->dropDownList($conveyortype_items, $params);
            ?>
      </div>
</div>
<div class="row">
      <div class="col-md-3 col-sm-3 col-lg-3">
            <?= $form->field($model, 'parent_id')->textInput() ?>
      </div>
</div>

<?php ActiveForm::end(); ?>


<?
$script1 = <<< JS
     
      if($location_type_id) {
            $("#location-location_type_id").prop( "value", $location_type_id );
            $("#location-location_type_id option[value!="+$location_type_id+"]").attr("disabled", "disabled");
      }     

      if(!$id) {
            if($location_type_id == 1) {
                  $(".field-location-parent_id").hide();
                  $(".field-location-conveyor_type_id").hide();
            }
            if($location_type_id == 2) {
                  hideAll();
            }
            if($location_type_id == 3) {
                  $(".field-location-parent_id").hide();
            }
            if($location_type_id == 4) {
                  hideAll();
            }
      }    

      if($id) {
            var location_type_id = $("#location-location_type_id").val();
            if(location_type_id == 1) {
                  $(".field-location-parent_id").hide();
                  $(".field-location-conveyor_type_id").hide();
            }
            if(location_type_id == 2) {
                  hideAll();
            }
            if(location_type_id == 3) {
                  $(".field-location-parent_id").hide();
            }
            if(location_type_id == 4) {
                  hideAll();
            }
            $("#location-location_type_id").attr("disabled", "disabled");
      }

      function hideAll() {
            $(".field-location-address").hide();
            $(".field-location-parent_id").hide();
            $(".field-location-conveyor_type_id").hide();
      }
JS;
$this->registerJs($script1);
?>