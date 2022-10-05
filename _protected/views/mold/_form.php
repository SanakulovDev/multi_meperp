<?php
use app\models\Part;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mold */
/* @var $form yii\widgets\ActiveForm */
$parts_select = Part::find()->where(['!=', 'state', Part::STATE_RAW])->all();
function fill_unit_select_box($parts_select) {
  $output = '';
  foreach($parts_select as $part) {
    echo $part->part_no.'<br>';
    $output .= '<option value="'.$part->part_no.'">'
      .$part->part_no.'</option>';
  }
  return $output;
}

$mpList = [];
$e = 0;
if($model->id) {
  foreach($moldpart as $value) {
    $mpList[$e]['id'] = $value->id;
    $mpList[$e]['mold_id'] = $value->mold_id;
    $mpList[$e]['part_id'] = $value->part_id;
    $mpList[$e]['quantity'] = $value->quantity;
    $e++;
  }
}
?>
<div class="mold-form">

  <?php
  $validationUrl = ['validate'];
  if(!$model->isNewRecord) {
    $validationUrl['id'] = $model->id;
  }
  $form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validateOnType' => false,
    'validationUrl' => $validationUrl,
    'options' => ['data-pjax' => true, 'class' => 'modalForm']
  ]); ?>

	<div class="row">
		<div class="col-md-4 col-sm-4 col-lg-4">
      <?=$form->field($model, 'mold_no')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-md-4 col-sm-4 col-lg-4">
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
          'class' => 'form-control input-sm'
        ]
      ]);
      ?>
		</div>
		<div class="col-md-4 col-sm-4 col-lg-4">
      <?=$form->field($model, 'project_name')->textInput(['maxlength' => true])?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-sm-4 col-lg-4">
      <?=$form->field($model, 'company_name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-md-4 col-sm-4 col-lg-4">
      <?=$form->field($model, 'part_number')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-md-4 col-sm-4 col-lg-4">
      <?=$form->field($model, 'part_name')->textInput(['maxlength' => true])?>
		</div>
	</div>
  <?
  if(isset($errorlist)) {
    echo '<div class="alert-danger alert fade in">';
    echo '<strong>'.Yii::t('app', 'Error').'</strong>';
    echo "<pre>Error:";
    print_r($errorlist);
    echo "</pre>";
    echo '</div>';
  }
  ?>

  <?php if(!$model->isNewRecord) : ?>
		<div class="row">
			<div class="col-sm-6 col-md-6 col-lg-6">
        <span class="form-control">
          <?=$model->createdBy->username.' '.$model->createdAtFormatted?>
        </span>
			</div>
			<div class="col-sm-6 col-md-6 col-lg-6">
        <span class="form-control">
          <?php if(!empty($model->updatedBy)) : ?>
            <?=$model->updatedBy->username?>
          <?php endif ?>
        </span>
			</div>
		</div>
  <?php endif ?>

	<hr/>
	<div id="frm_scents">
		<div class="row" id="p_scents">
			<div class="form-group col-md-5 col-lg-5">
        <?=Html::activeLabel($model, Yii::t('app', 'Part'))?>
				<!--        <select name="items[0][machine]" class="form-control item-unit select2" id="Fld_Machine(0)">-->
				<!--          <option></option>--><?php //echo fill_unit_select_box($parts_select); ?>
				<!--        </select>-->
			</div>
			<div class="form-group col-md-5 col-lg-5">
        <?=Html::activeLabel($model, Yii::t('app', 'Quantity'))?>
				<!--        <input type="text" class="form-control" name="items[0][quantity]" id="Fld_Quantity(0)" />-->
				<!--        <input type="text" class="form-control" name="items[0][id]" id="Fld_id(0)" />-->
				<!--	      <a class="modalButtonDelete"-->
				<!--		      id="Fld_del_id"-->
				<!--		      title="Удалить"-->
				<!--		      data-href="/mold/part-delete?id=5"-->
				<!--		      data-grid="pjaxGrid">-->
				<!--	      <span class="glyphicon glyphicon-trash"></span>-->
				<!--	      </a>-->
			</div>
			<a href="#" id="addScnt"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
		</div>
	</div>
</div>

</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
	var id      = <?php echo $model->id ? 1 : 0 ?>;
	var scntDiv = $('#frm_scents')
	var i       = $('#p_scents').length + 1
	$(document).ready(function (){
		$('#addScnt').click(function (){
			var index = 0
			$(
				'<div class="row" id="p_scents">' +
				'<div class="col-md-5 col-lg-5 form-group">' +
				'<select name="items[(' + i + 1 + ')][machine]"' +
				'class="form-control item-unit select2" id="Fld_Machine(' + i + ')"><option>' +
				'</option><?php echo fill_unit_select_box($parts_select); ?></select>' +
				'</div>' +
				'<div class="col-md-5 col-lg-5 form-group">' +
				'<input type="text" class="form-control" name="items[(' + i + 1 + ')][quantity]" id="Fld_Quantity(' + i + ')"/>' +
				'</div><a href="#" id="remScnt" onclick="removeCont(this);"><i class="fa fa-minus-circle" aria-hidden="true"></i> Remove</a></div>',
			).appendTo(scntDiv)
			i++
			index++
			$('.select2').select2()
			return false
		})
	})

	function removeCont(_this){
		var thisId = _this.id
		var delId  = thisId.substr(thisId.indexOf('p') + 1)
		$.ajax(
			{
				url: '/mold/part-delete',
				type: 'post',
				data: {
					id: delId,
				},
				success: function (response){
					if(response.status == 1){
						$(_this).parent().remove()
					}
				},
				error: function (xhr){
					console.log(xhr)
				},
			})
	}

	// Update
	if(id == 1){
		var moldpartList = <?php echo json_encode((object)$mpList) ?>;
		var count_it     = <?php echo count($mpList) ?>;
		if(moldpartList){
			for(var i = 0; i < count_it; i++){
				$(
					'<div class="row" id="p_scents">' +
					'<div class="col-md-5 col-lg-5 form-group">' +
					'<select name="items[(' + i + 1 + ')][machine]"' +
					'class="form-control item-unit select2" id="Fld_Machine(' + i + ')"><option selected="selected">' + moldpartList[i].part_id +
					'</option><?php echo fill_unit_select_box($parts_select); ?></select>' +
					'</div>' +
					'<div class="col-md-5 col-lg-5 form-group">' +
					'<input type="text" class="form-control" name="items[(' + i + 1 + ')][quantity]" id="Fld_Quantity(' + i + ')" value=' + moldpartList[i].quantity + '>' +
					'<input type="hidden" class="form-control" name="items[(' + i + 1 + ')][id]" id="Fld_id(' + i + ')" value=' + moldpartList[i].id + '>' +
					'</div>' +
					'<a href="#"' +
					'id="p' + moldpartList[i].id + '"' +
					'onclick="removeCont(this);">' +
					'<i class="fa fa-minus-circle" aria-hidden="true"></i> ' +
					'Remove </a>' +
					'</div>',
				).appendTo(scntDiv)
			}
		}
		$('.select2').select2()
	}
</script>