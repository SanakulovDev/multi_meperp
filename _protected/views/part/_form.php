<?php
use app\models\ContractSource;
use app\models\PartColor;
use app\models\PartMark;
use app\models\PartType;
use app\models\ProductGroup;
use app\models\ProductLine;
use app\models\ProductModel;
use app\models\Unit;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Part */
/* @var $form yii\widgets\ActiveForm */

$cacheTtl = 3600;

$unitsAll = Yii::$app->cache->getOrSet('unitsAll', function () {
  return Unit::find()->all();
}, $cacheTtl);

$partTypesAll = Yii::$app->cache->getOrSet('partTypesAll', function () {
  return PartType::find()->all();
}, $cacheTtl);

$partMarksAll = Yii::$app->cache->getOrSet('partMarksAll', function () {
  return PartMark::find()->all();
}, $cacheTtl);

$partColorsAll = Yii::$app->cache->getOrSet('partColorsAll', function () {
  return PartColor::find()->all();
}, $cacheTtl);

$contractSourceAll = Yii::$app->cache->getOrSet('contractSourcesAll', function () {
  return ContractSource::find()->all();
}, $cacheTtl);

$warehouses = Warehouse::find()->where(['<>', 'warehouse_type', Warehouse::TYPE_VIRTUAL])->all();

$prompt = ['prompt' => '. . .'];

?>

<div class="part-form">

  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'state')->dropDownList($model->stateList, ['prompt' => '. . .', 'id' => 'state_id']) ?>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'part_no')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'unit_id')->dropDownList(ArrayHelper::map($unitsAll, 'id', 'description'), $prompt);?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-sm-6 col-lg-6">
      <?php
      $markItems = ArrayHelper::map($partMarksAll, 'name', 'name');
      $params = [
        'class' => ' form-control select2',
        'prompt' => Yii::t('app', 'Select'),
      ];
      echo $form->field($model, 'part_name')->dropDownList($markItems, $params);
      ?>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6">
      <?php
      $colorItems = ArrayHelper::map($partColorsAll, 'name', 'name');
      echo $form->field($model, 'part_color')->dropDownList($colorItems, $params);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?php
      $items = ArrayHelper::map($partTypesAll, 'id', 'typename');
      $params = ['prompt' => '. . .'];
      echo $form->field($model, 'part_type_id')->dropDownList($items, $params);
      ?>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
        <?php
          $items = ArrayHelper::map($warehouses, 'id', 'name');
          echo $form->field($model, 'warehouse_id')->dropDownList($items, $params);
        ?>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form
        ->field($model, 'pack_size')
        ->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Split pack sizes by comma')]) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
    </div>
    <div class="col-md-4 col-sm-4 col-lg-4">
      <?php
      $items = ArrayHelper::map($contractSourceAll, 'id', 'name');
      echo $form->field($model, 'contract_source_id')->dropDownList($items, $prompt);
      ?>
    </div>

  </div>

  <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
      <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
    </div>
  </div>

  <div class="form-group pull-right">
    <?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
    <?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>

<script>
  var newModel = <?php echo $model->id ? 1 : 0; ?>;
  stateValue = document.getElementById("state_id").value;
</script>
