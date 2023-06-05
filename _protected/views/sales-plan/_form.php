<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PartColor */
/* @var $form yii\widgets\ActiveForm */
/* @var $partMarksAll */
/* @var $partColorsAll */
/* @var $customersAll */

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

    $parts = $model->isNewRecord ? [] : [$model->part_id=>$model->part->partinfo];
?>

<?= $form->field($model, 'customer_id')->dropDownList($customersAll, ['class' => 'form-control select2'])?>
<div class="row">
  <div class="col-md-6">
    <?= $form->field($model, 'partMarkId')->dropDownList($partMarksAll, ['class' => 'form-control select2 finder'])?>
  </div>
  <div class="col-md-6">
    <?= $form->field($model, 'partColorId')->dropDownList($partColorsAll, ['class' => 'form-control select2 finder'])?>
  </div>
</div>
<?= $form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2'])?>

  <div class="row">
    <div class="col-md-6">
      <?= $form->field($model, 'target_qty')->textInput(['class' => 'form-control', 'type'=>'number'])?>
    </div>
    <div class="col-md-6">
      <?=$form->field($model, 'target_date')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm',
          'startView' => 'year',
          'minView' => 'year',
          'maxView' => 'year',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => 'YYYY-MM',
          'class' => ' form-control'
        ]
      ])->label(Yii::t('app', 'Issued date'));
      ?>
    </div>
  </div>

<?php
  ActiveForm::end();
  $partsUrl = Url::to(['part/get-parts-by-mark-and-color'], true);
$script_create = <<< JS
    $('.finder').on('change', function(e) {
      load();
    })
    load();
    function load() {
      var color = $('#salesplan-partcolorid').val();
      var mark = $('#salesplan-partmarkid').val();
      var url = "$partsUrl?mark="+mark+"&color="+color;
      $.ajax({
        dataType: "json",
        type: "GET",
        url: url,
        success: function(items){
          $('#salesplan-part_id').empty().select2({ "data": items });        
        }
      });
    }
JS;
  $this->registerJs($script_create);
?>