<?php
use app\components\Helpers;
use yii\helpers\Html;
use app\models\Part;
use app\models\Warehouse;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$notFgParts = ArrayHelper::map(
  Part::find()
    ->where(['state' => [Part::STATE_SEMI, Part::STATE_RAW, Part::STATE_CASTLE]])
    ->all(),
  'id',
  'partinfo'
);
$warehouses = ArrayHelper::map(
  Warehouse::find()
    ->where(['status' => Warehouse::STATUS_ACTIVE])
    ->all(),
  'id',
  'name'
);

$whDefault = count($warehouses) > 0 ? array_key_first($warehouses) : null;
?>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-6"><?= Yii::t('app', 'Details') ?>
          </div>
          <div class="col-md-6">
            <button type="button" class="btn btn-success btn-sm pull-right btnAddDetail "
              title="<?= Yii::t('app', 'Add new detail (F2)') ?>">
              <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            </button>
          </div>
        </div>
      </div>
      <?php if (is_array($errorlist) and count($errorlist) > 0): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> <?= Yii::t('app', 'Correct the following errors.') ?>
        </h4>
        <?php if (is_array($errorlist['details']) and count($errorlist['details']) > 0) {
          foreach ($errorlist['details'] as $key => $errList) {
            if (!in_array($key, ['no_item'])) {
              echo '<b>' . $key . ' - строка :</b><br/>';
            }
            foreach ($errList as $err) {
              foreach ($err as $e) {
                echo ' - ' . $e . '<br/>';
              }
            }
            echo '<br/>';
          }
        } ?>
      </div>
      <?php endif; ?>

      <table class="table" id="detailTable">
        <tr>
          <th style="width: 20px">№</th>
          <th style="width: 300px"><?= Yii::t('app', 'Part number') ?></th>
          <th style="width: 300px"><?= Yii::t('app', 'Product specification') ?></th>
          <th style="width: 150px">
            <?= Yii::t('app', 'Quantity') ?> <span id="total_detail_qty" class="badge">0</span>
          </th>
          <th><?= Yii::t('app', 'Shop or line') ?></th>
          <th style="width: 20px"><?= Yii::t('app', 'Action') ?></th>
        </tr>

        <tr id="tr_template" style="display: none">
          <?= Html::input('hidden', 'items[id][]', '', ['class' => 'form-control']) ?>
          <th scope="row" style="text-align:  center;vertical-align:  middle;">
            <input type="hidden" name="items[num][]" value="">
          </th>
          <td>
            <?= Html::dropDownList('items[detail][]', null, $notFgParts, [
              'id' => 'partlist',
              'class' => 'form-control detail_part partSelect',
              'prompt' => Yii::t('app', 'Select...'),
            ]) ?>
          </td>
          <td>
            <?= Html::dropDownList(
              'items[spec][]',
              null,
              [],
              ['id' => 'speclist', 'class' => 'form-control detail_spec', 'prompt' => Yii::t('app', 'Select...')]
            ) ?>
          </td>
          <td>
            <?= Html::input('text', 'items[quantity][]', '', ['class' => 'form-control detail-qty']) ?>
          </td>
          <td><?= Html::dropDownList('items[warehouse][]', $whDefault, $warehouses, [
            'id' => 'partlist',
            'class' => 'form-control detail_part',
            'prompt' => Yii::t('app', 'Select...'),
          ]) ?>
          </td>
          <td style="text-align: center;vertical-align: middle">
            <span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
          </td>
        </tr>

        <?php if (isset($items['detail'])) {
          if (is_array($items['detail']) and count($items['detail']) > 1) {
            foreach ($items['detail'] as $key => $value) {
              if ($key == 0) {
                continue;
              } ?>
        <tr class="tr_item">
          <?= Html::input('hidden', 'items[id][]', $items['id'][$key] ?? null, ['class' => 'form-control']) ?>
          <th scope="row" style="text-align:  center;vertical-align:  middle;">
            <input type="hidden" name="items[num][]" value="<?= $key ?>"><?= $key ?>
          </th>
          <td>
            <?= Html::dropDownList('items[detail][]', $items['detail'][$key], $notFgParts, [
              'class' => 'form-control detail_part partSelect',
              'prompt' => Yii::t('app', 'Select...'),
            ]) ?>
          </td>
          <td>
            <?php
            $specVal = isset($items['spec'][$key]) ? $items['spec'][$key] : 0;
            echo Html::dropDownList(
              'items[spec][]',
              $specVal,
              [],
              ['class' => 'form-control detail_spec', 'data-spec' => $specVal, 'prompt' => Yii::t('app', 'Select...')]
            );
            ?>
          </td>
          <td>
            <?= Html::input('text', 'items[quantity][]', $items['quantity'][$key], [
              'class' => 'form-control detail-qty',
            ]) ?>
          </td>
          <td>
            <?= Html::dropDownList('items[warehouse][]', $items['warehouse'][$key], $warehouses, [
              'class' => 'form-control detail_part',
              'prompt' => Yii::t('app', 'Select...'),
            ]) ?>
          </td>
          <td style="text-align: center;vertical-align: middle">
            <span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
          </td>
        </tr>
        <?php
            }
          }
        } ?>

      </table>
      <div class="panel-footer">
        <p>
          &nbsp;
          <button type="button" class="btn btn-success btn-sm pull-right btnAddDetailFooter"
            title="<?= Yii::t('app', 'Add new detail (F2)') ?>">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
          </button>
        </p>
      </div>
    </div>
  </div>
</div>

<?php
$fetchSpecUrl = Url::to(['product-specification/fetch-by-part'], true);
$prompt = Yii::t('app', 'Select...');

$script1 = <<<JS
  
  $("#detailTable").on("change", ".partSelect", function(e){
    var elemant = $(this);
    var part_id = $(this).val();
    if(part_id == undefined || part_id == null || part_id == '') return;
    var url = "$fetchSpecUrl?id="+part_id;
    $.ajax({
      dataType: "json",
      type: "GET",
      url: url,
      success: function(items){
        elemant.closest("tr").find("select.detail_spec").find('option').remove();
        elemant.closest("tr").find("select.detail_spec")
          .append($("<option></option>")
          .attr("value", 0)
          .text("$prompt")); 
        val = elemant.closest("tr").find("select.detail_spec").data("spec");

        $.each(items, function(key, value) {   
          elemant.closest("tr").find("select.detail_spec")
          .append($("<option></option>")
          .attr("value",value.id)
          .attr("style","color:"+value.status)
          .attr("selected", value.id==val)
          .text(value.text)); 
        });
      }
    });
  });
  $(".detail_part:not(#tr_template td .detail_part)").select2();
  $(".detail_part").trigger('change');
  function calculateTotalQty(){
    var sum = 0;
    $('tr.tr_item input.detail-qty').each(function(){
        sum += parseFloat(this.value);
    });
    $('#total_detail_qty').text(sum.toFixed(2));
  }
  
  calculateTotalQty();
  $(document).on('change', 'tr.tr_item input.detail-qty', function (){
    calculateTotalQty();
  })
  
JS;
$this->registerJs($script1);

