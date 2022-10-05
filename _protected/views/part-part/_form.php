<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductParts */
	/* @var $form yii\widgets\ActiveForm */

	/** @var TYPE_NAME $parentParts */
	/** @var TYPE_NAME $notRawParts */
	/** @var TYPE_NAME $notFgParts */
	/** @var TYPE_NAME $parts */
	/** @var TYPE_NAME $warehouses */
?>

<?php
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
	<div class="col-lg-4 col-sm-4">
		<?=
			$form->field($model, 'part_id')->dropDownList($parentParts, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-lg-4 col-sm-4">
		<?=$form->field($model, 'sub_part_id')->dropDownList($notFgParts, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-lg-4 col-sm-4">
		<?=$form->field($model, 'usage_qty')->textInput()?>
	</div>
</div>

<div class="row">
	<div class="col-lg-4 col-sm-4">
		<?=$form->field($model, 'warehouse_id')->dropDownList($warehouses, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-lg-4 col-sm-4">
		<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
	</div>
	<div class="col-lg-4 col-sm-4">
		<?=$form->field($model, 'remark')->textInput()?>
	</div>
</div>

<?php
	ActiveForm::end();
	$script_create = <<< JS
    $(".select2").select2();
JS;
	$this->registerJs($script_create);

if($model->isNewRecord){  
	$script_create = <<< JS
    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
    $("#partpart-part_id").val(getUrlVars()["PartPartSearch%5Bpart_id%5D"]);
JS;
	$this->registerJs($script_create);
}
?>
