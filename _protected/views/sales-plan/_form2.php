<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
/* @var $this yii\web\View */
/* @var $model app\models\PartColor */
/* @var $form yii\widgets\ActiveForm */
/* @var $partMarksAll */
/* @var $partColorsAll */
/* @var $customersAll */

    $validationUrl = ['validate'];
    // if(!$model->isNewRecord){
    //     $validationUrl['id'] = $model->id;
    // }
    $form = ActiveForm::begin([
        'id' => 'dynamic-form',
        'enableAjaxValidation' => true,
        'validateOnType' => false,
        'validationUrl' => $validationUrl,
        'options' => ['data-pjax' => true, 'class' => 'modalForm']
    ]);

    $parts = $model->isNewRecord ? [] : [$model->part_id=>$model->part->partinfo];
?>

<?php ob_start();?>
  $(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
      $(item).find('.finder-partmarkid').select2();
      $(item).find('.finder-partColorId').select2();
      $(item).find('.datetimepicker').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: true
        // Boshqa sozlovlar va parametrlar shu yerdan kiritilishi mumkin
      });
  });

  $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
      $(item).find('.finder-partmarkid').select2();
      $(item).find('.finder-partColorId').select2();
      $(item).find('.datetimepicker').datetimepicker({
        format: 'yyyy-mm',
        autoclose: true,
        todayBtn: true,
        startView: 'year',
        minView: 'year',
        maxView: 'year',
        // Boshqa sozlovlar va parametrlar shu yerdan kiritilishi mumkin
      });
  });

  $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
      if (! confirm("Are you sure you want to delete this item?")) {
          return false;
      }
      return true;
  });

  $(".dynamicform_wrapper").on("afterDelete", function(e) {
      console.log("Deleted item!");
  });

  $(".dynamicform_wrapper").on("limitReached", function(e, item) {
      alert("Limit reached");
  });
<?php $this->registerJs(ob_get_clean());?>


<?= $form->field($modelMain, 'customer_id')->dropDownList($customersAll, ['class' => 'form-control select2'])?>

<?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 15, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $models[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'partMarkId',
            'partColorId',
            'part_id',
            'target_qty',
            'target_date'
        ],
    ]); ?>
    <div class="panel panel-default">
      <div class="panel-heading">
          <a href="javascript::void(0)" class="pull-right add-item btn btn-success btn-xs"><?=Yii::t('app', 'btn-create')?></i></a>
          <div class="clearfix"></div>
      </div>
      <div class="panel-body container-items" style="overflow-y: scroll; max-height: 350px;">

          <?php foreach ($models as $index => $model): ?>
                  <div class="item panel panel-default"><!-- widgetBody -->
                      <?php
                          // necessary for update action.
                          if (!$model->isNewRecord) {
                              echo Html::activeHiddenInput($model, "[{$index}]id");
                          }
                      ?>
                        <div class="row" style="display: flex;align-items: center;justify-content: center;">
                          <div class="col-md-2">
                            <?= $form->field($model, "[{$index}]partMarkId")->dropDownList($partMarksAll, ['class' => "form-control select2 finder-partmarkid", 'prompt'=>'---'])?>
                          </div>
                          <div class="col-md-2">
                            <?= $form->field($model, "[{$index}]partColorId")->dropDownList($partColorsAll, ['class' => 'form-control select2 finder-partColorId', 'prompt'=>'---'])?>
                          </div>
                          <div class="col-md-2">
                            <?= $form->field($model, "[{$index}]part_id")->dropDownList($parts, ['class' => 'form-control select2'])?>
                          </div>
                          <div class="col-md-2">
                            <?= $form->field($model, "[{$index}]target_qty")->textInput(['class' => 'form-control', 'type'=>'number'])?>
                          </div>
                          <div class="col-md-2">
                            <?=$form->field($model, "[{$index}]target_date")->widget(DateTimePicker::classname(), [
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
                                    'class' => ' form-control datetimepicker'
                                  ]
                                ])->label(Yii::t('app', 'Issued date'));
                                ?>
                          </div>
                          <div class="col-md-1">
                                <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                  </div>
          <?php endforeach;?>
                  

      </div>
    </div>

    <?php    DynamicFormWidget::end();?>
<?php
  ActiveForm::end();
  $partsUrl = Url::to(['part/get-parts-by-mark-and-color'], true);
  ob_start();?>
    $('body').on('change', '.finder-partmarkid', function(e) {
      let partMark = $(this).val();
      let partMarkId = $(this).attr('id');
      let id = partMarkId.match(/\d+/)[0];
      let partColor = $('#salesplan-'+id+'-partcolorid').val();

      let partId = 'salesplan-'+id+'-part_id';
      load(partMark, partColor, partId);
    })


    $('body').on('change', '.finder-partColorId', function(e) {
      let partColor = $(this).val();
      let partColorId = $(this).attr('id');
      let id = partColorId.match(/\d+/)[0];
      let partMark = $('#salesplan-'+id+'-partmarkid').val();

      let partId = 'salesplan-'+id+'-part_id';
      load(partMark, partColor, partId);
    })

    function load(mark, color, partId) {
      var url = "<?= $partsUrl?>?mark="+mark+"&color="+color;
      $.ajax({
        dataType: "json",
        type: "GET",
        url: url,
        success: function(items){
          $('#'+partId).empty().select2({ "data": items });        
        }
      });
    }
<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY )
?>