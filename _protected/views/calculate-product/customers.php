<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;

?>
<div class="calculate-product-customers">
    <?php $form = ActiveForm::begin();?>
      <div class="row" style="display: flex; align-items:center;">
        <div class="col-md-3">
          <?=$form->field($model, 'customerId')->dropdownList($customers, ['prompt'=>'---', 'class'=>'form-control select2'])?>
        </div>
        
        <div class="col-md-3">
          <?=$form->field($model, 'fromDate')->widget(DateTimePicker::classname(), [
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
          ]);?>
        </div>

        <div class="col-md-3">
        <?=$form->field($model, 'toDate')->widget(DateTimePicker::classname(), [
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
          ]);?>
        </div>
        <div class="col-md-3">
          <?= Html::submitButton('Ok', ['class'=>'btn btn-primary submit'])?>
        </div>
      </div>

    <?php ActiveForm::end();?>
    <div class="row" style="display: flex; align-items:center;">
      <div class="col-md-7">
        <h3 style="font-weight: bold;">Отчет о содержании Счет-фактуры на основе информации ТТН на 2023 </h3>
      </div>
      <div class="col-md-3" style="text-align: right;">
          <button class="btn btn-primary term-btn active" data-id="1" data-active="1">Подробный</button>
          <button class="btn  term-btn" data-id="2" data-active="0">Сводный</button>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success export-btn"><?= Yii::t('app', 'btn-download')?></button>
      </div>
    </div>
    <div class="dashboard">

    </div>
</div>

<?php ob_start();?>
$(function() {
  $('.submit').on('click', function(e) {
      e.preventDefault();
      ajaxRequest('POST', function(data){
        $('.dashboard').html(data);
      });
  })

  // term btn click
  $('.term-btn').on('click', function(){
    let id = $(this).data('id');
    $('.term-btn').attr('data-active', 0);
    $(this).attr('data-active', 1);
    $('.term-btn').removeClass('btn-primary');
    $('.term-btn').removeClass('active');

    $(this).addClass('btn-primary');
    $(this).addClass('active');

    ajaxRequest('POST', function(data){
      $('.dashboard').html(data);
    });
  })
  function ajaxRequest(type='POST', callback){
    let url         = '<?= Url::to('/calculate-product/customers-ajax')?>';
    let customerId = $('#calculateproduct-customerid').val();
    let fromDate    = $('#calculateproduct-fromdate').val(); 
    let toDate      = $('#calculateproduct-todate').val(); 
    let term        = $('.term-btn[data-active=1]').data('id');
    let data = {
      customerId: customerId,
      fromDate: fromDate,
      toDate: toDate,
      term: term
    };
    $.ajax({
      url: url,
      type: type,
      data: data,
      success: function(data){
        callback(data);
      },
      error: function(){
        alert("Xatolik sodir bo'ldi" );
      }
    })
  }
})

<?php $this->registerJs(ob_get_clean());