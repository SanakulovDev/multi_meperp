<?php
  use kartik\datetime\DateTimePicker;
  use yii\helpers\Html;
  use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
  /* @var $model app\models\ContainerInvoice */
  $this->title = Yii::t('app', 'Update regime') . ': ' . $model->invoice->invoice_no . '(' . $model->container->container_no . ')';
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = ['label' => $model->invoice->invoice_no . '(' . $model->container->container_no . ')', 'url' => ['view', 'container_id' => $model->container_id, 'invoice_id' => $model->invoice_id, 'shipped_at' => $model->shipped_at]];
  $this->params['breadcrumbs'][] = Yii::t('app', 'Update regime');
?>
<div class="container-invoice-update">
    <?php $form = ActiveForm::begin();?>
    <div class="row">
      <div class="col-lg-3">
        <?
          $params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
          echo $form->field($model, 'regime')->dropDownList(\app\models\ContainerInvoice::$regimeList, $params)
            ->label(Yii::t('app', 'Customs regime'));
        ?>
      </div>
     <div class="col-lg-3">
        <?=
          $form->field($model, 'passed_at')->widget(DateTimePicker::classname(), [
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
              'value' => date('Y-m-d'),
              'autocomplete' => 'off',
              'placeholder' => 'YYYY-MM-DD',
              'class' => ' form-control'
            ]
          ])->label(Yii::t('app', 'Passed at'));
        ?>
      </div>
    </div>
    <div class="form-group pull-right">
      <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
      <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
    </div>
<?php ActiveForm::end();?>
</div>
