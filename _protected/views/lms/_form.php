<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Lms */
	/* @var $form yii\widgets\ActiveForm */
	$validationUrl = ['validate'];
	if(!$model->isNewRecord){
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
	<div class="col-sm-6 col-md-6 col-lg-6">
		<?=$form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-sm-6 col-md-6 col-lg-6">
		<?=$form->field($model, 'supplier_id')->dropDownList($suppliers, ['class' => 'form-control select2'])?>
	</div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
      <?=$form->field($model, 'warehouse_id')->dropDownList($warehouses, ['class' => 'form-control select2'])?>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
      <?=$form->field($model, 'dloc')->textInput(['maxlength' => true])?>
    </div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'minimum')->textInput(['maxlength' => true])?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'maximum')->textInput(['maxlength' => true])?>
	</div>
    <div class="col-sm-4 col-md-4 col-lg-4">
      <?=$form->field($model, 'bms')->dropDownList($model->getSizeList(), ['class' => 'form-control'])?>
    </div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'stack')->textInput(['maxlength' => true])?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'mpr')->textInput(['maxlength' => true])?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'high_theft')->dropDownList($model->highTheftList(), ['class' => 'form-control'])?>
	</div>
</div>

<?php if(!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('created_by').' '.$model->createdBy->fullname.' '.$model->createdAtFormatted?>
			</span>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('updated_by').' '.$model->updatedBy->fullname.' '.$model->updatedAtFormatted?>
			</span>
		</div>
	</div>
<?php endif ?>


<?php ActiveForm::end(); ?>

<?php
$url = Url::to(['supplier/list-by-part-contract'], true);
$partId = $model->part_id;
$js = <<< JS
$(document).ready(function() {
  if('$partId'){
    let url = '$url'+'?id=$partId';
    $.get(url, function(data, status){
	  $('#lms-supplier_id').find('option').remove();
	  $.each(data, function () {
	    $('#lms-supplier_id').append(new Option(this.text, this.id));
	  });
	 });
  }
  $(document).on("select2:select", "#lms-part_id", function(e) {
	 let data = e.params.data;
	 let url = '$url'+'?id='+data.id;
	 $.get(url, function(data, status){
	  $('#lms-supplier_id').find('option').remove();
	  $.each(data, function () {
	    $('#lms-supplier_id').append(new Option(this.text, this.id));
	  });
	 });
	});
});
JS;

$this->registerJs($js, yii\web\View::POS_END);
?>

