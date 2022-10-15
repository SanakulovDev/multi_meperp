<?
use app\assets\AdminLteAsset;
use app\enums\ContainerType;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\JqueryAsset;
?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p>
          <?=Yii::t('app', 'Details')?>
					<button type="button" class="btn btn-success btn-sm pull-right" id="btnAddContDetail">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>
				</p>
			</div>
      <? if(is_array($errorlist) && count($errorlist) > 0) { ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
          <?
          foreach($errorlist as $key => $errList) {
            if(!in_array($key, ['no_item', 'cant_delete'])) {
              echo '<b>'.$key.' - строка :</b><br/>';
            }
            foreach($errList as $err) {
              foreach($err as $e) {
                echo ' - '.$e.'<br/>';
              }
            }
            echo "<br/>";
          }
          ?>
				</div>
      <? } ?>
      <?
      $containerTypeList = ContainerType::list();
      $containerTypeParams = ['class' => 'form-control input-sm cont-type-val'];
      ?>
			<table class="table" id="inv_cont_ship">
				<tr>
					<th>№</th>
					<th><?=Yii::t('app', 'CONT./TRUCK/AWBL')?></th>
					<th class="cont-type"><?=Yii::t('app', 'Container type')?></th>
					<th><?=Yii::t('app', 'Shipped at')?></th>
					<th><?=Yii::t('app', 'Approximate arrival date')?></th>
					<th style="width: 20px"><?=Yii::t('app', 'Action')?></th>
				</tr>
				<tr id="tr_template" style="display: none">
					<th scope="row" style="text-align:  center;vertical-align:  middle;">
						<input type="hidden" name="items[num][]"></th>
					<td><?=Html::input('text', 'items[container_no][]', '', ['class' => 'form-control input-sm', 'value' => '01148QGA']);?></td>
					<td class="cont-type">
            <?=Html::dropDownList('items[container_type][]', null, $containerTypeList, $containerTypeParams)?>
					</td>
					<td>
            <?=Html::input('text', 'items[ship_dt][]', '', ['placeholder' => 'YYYY-MM-DD', 'class' => 'form-control input-sm datetimepicker', 'autocomplete' => 'off']);?>
					</td>
					<td>
            <?=Html::input('text', 'items[app_arr_at][]', '', ['placeholder' => 'YYYY-MM-DD', 'class' => 'form-control input-sm datetimepicker', 'autocomplete' => 'off']);?>
					</td>
					<td style="text-align: center;vertical-align: middle">
						<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
					</td>
				</tr>

        <?
	      /**
        if(isset($items['container_no']) && count($items['container_no']) > 1) {
          foreach($items['container_no'] as $key => $value) {
            if($key == 0) {
              continue;
            } ?>
						<tr class="tr_item">
							<th scope="row" style="text-align:  center;vertical-align:  middle;">
								<input type="hidden" name="items[num][]" value="<?=$key?>"><?=$key?>
							</th>
							<td><?=Html::input('text', 'items[container_no][]', $items['container_no'][$key], ['class' => 'form-control']);?></td>
							<td class="cont-type">
                <?=Html::dropDownList('items[container_type][]', $items['container_type'][$key], $containerTypeList, $containerTypeParams)?>
							</td>
							<td>
                <?=DateTimePicker::widget(
                  [
                    'name' => 'items[ship_dt][]',
                    'value' => $items['ship_dt'][$key],
                    'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                    'layout' => '{picker}{input}{remove}',
                    'removeButton' => ['position' => 'append'],
                    'language' => 'ru',
                    'convertFormat' => true,
                    'options' => ['placeholder' => 'YYYY-MM-DD',
                      'class' => ' form-control'
                    ],
                    'pluginOptions' => [
                      'format' => 'yyyy-mm-dd',
                      'autoclose' => true,
                      'todayHighlight' => true,
                      'startView' => 'month',
                      'minView' => 'month',
                      'maxView' => 'month'
                    ]
                  ]
                );?>
							</td>
							<td>
                <?=DateTimePicker::widget(
                  [
                    'name' => 'items[app_arr_at][]',
                    'value' => $items['app_arr_at'][$key],
                    'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                    'layout' => '{picker}{input}{remove}',
                    'removeButton' => ['position' => 'append'],
                    'language' => 'ru',
                    'convertFormat' => true,
                    'options' => ['placeholder' => 'YYYY-MM-DD',
                      'class' => ' form-control'
                    ],
                    'pluginOptions' => [
                      'format' => 'yyyy-mm-dd',
                      'autoclose' => true,
                      'todayHighlight' => true,
                      'startView' => 'month',
                      'minView' => 'month',
                      'maxView' => 'month'
                    ]
                  ]
                );
                ?>
							</td>
							<td style="text-align: center;vertical-align: middle">
								<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
							</td>
						</tr>
          <? }
        }
        */
        ?>
			</table>
		</div>
	</div>
</div>
<?
$this->registerCssFile("@themes/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.css", ['depends' => [AdminLteAsset::className()]]);
//$this->registerJsFile("@themes/bower_components/bootstrap/dist/js/bootstrap.min.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.uz-latn.min.js", ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile("@themes/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js", ['depends' => [JqueryAsset::className()]]);
?>
<?php
$lang = (Yii::$app->language == 'uz') ? 'uz-latn' : Yii::$app->language;
$script = <<< JS
$(document).ready(function() {
  $(document).on("click", "#btnAddContDetail", function(e) {
		$('.datetimepicker').datepicker({
			language: "$lang",
			format: 'yyyy-mm-dd',
			clearBtn: true,
			autoclose: true,
			todayHighlight: true
		});
	});
});
JS;
$this->registerJs($script);
?>
