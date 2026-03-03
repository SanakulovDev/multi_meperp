<?php
use app\models\ContractSource;
use app\models\PartType;
use app\models\ProductLine;
use app\models\Warehouse;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'System setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'System setting'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$whs = Warehouse::find()->select(["id", "CONCAT(name, '(',description,')' ) as name"])->orderBy('name')->all();
$wh_items = ArrayHelper::map($whs, 'id', 'name');
$wh_params = ['prompt' => '. . .', 'class' => 'form-control input-sm  select2', 'multiple' => true, 'size' => 7];
$c_s = ContractSource::find()->select(["id", "name"])->orderBy('name')->all();
$c_s_items = ArrayHelper::map($c_s, 'id', 'name');
$c_s_params = ['prompt' => '. . .', 'class' => 'form-control input-sm  select2', 'multiple' => true, 'size' => 7];
$parttypes = PartType::find()->select(["id", "typename"])->orderBy('typename')->all();
$parttype_items = ArrayHelper::map($parttypes, 'id', 'typename');
$p_l = ProductLine::find()->select(["id", "linename"])->orderBy('linename')->all();
$p_l_items = ArrayHelper::map($p_l, 'id', 'linename');
$p_l_params = ['prompt' => '. . .', 'class' => 'form-control input-sm  select2', 'multiple' => true, 'size' => 7];
?>
<style>
  .w60{width:30px; min-width:30px; max-width:60px;}
  .w70{width:70px; min-width:70px; max-width:70px;}
  .margin-14_0_10_0{margin:-14px 0 -10px 0}
</style>
<div class="setting-index">

  <div class="setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row m_top10">
      <div class="col-xs-12">
        <fieldset class="scheduler-border">
          <legend class="scheduler-border">
            <span><?=Yii::t('app', 'Shift')?></span>
          </legend>

          <div class="row margin-14_0_10_0">

            <div class="col-xs-12 col-sm-4">
              <div class="input-group input-group-sm">
                  <span class="input-group-addon w70">
                    <strong><?=Yii::t('app', 'Hour').":";?></strong>
                  </span>
                <input type="number" name="shift_hour" class="form-control w60" value="<?=$all_params['shift_hour']?>">
              </div>
            </div>

            <div class="col-xs-12 col-sm-4">
              <div class="input-group input-group-sm">
                  <span class="input-group-addon w70">
                    <strong><?="1-".Yii::t('app', 'Begin').":";?></strong>
                  </span>
                <input type="text" name="shift1_begin" class="form-control w60" value="<?=$all_params['shift1_begin']?>">
              </div>
            </div>

            <div class="col-xs-12 col-sm-4">
              <div class="input-group input-group-sm">
                  <span class="input-group-addon w70">
                    <strong><?="2-".Yii::t('app', 'Begin').":";?></strong>
                  </span>
                <input type="text" name="shift2_begin" class="form-control w60" value="<?=$all_params['shift2_begin']?>">
              </div>
            </div>


          </div>

        </fieldset>
      </div>
    </div>

    <div class="row m_top10">
      <div class="col-md-4">
        <?=Html::label('Company name')?>
        <?=Html::input('text', 'comp_name', $all_params['comp_name'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-2">
        <?=Html::label('comp_short_name')?>
        <?=Html::input('text', 'comp_short_name', $all_params['comp_short_name'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-2">
        <?=Html::label('account_suffix')?>
        <?=Html::input('text', 'account_suffix', $all_params['account_suffix'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-3">
        <?=Html::label('DUNS')?>
        <?=Html::input('text', 'comp_duns', $all_params['comp_duns'], $options = ['class' => 'form-control'])?>
      </div>

    </div>

    <div class="row m_top10">
      <div class="col-md-3">
        <?=Html::label('Admin-email')?>
        <?=Html::input('text', 'adminEmail', $all_params['adminEmail'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-3">
        <?=Html::label('Support-email')?>
        <?=Html::input('text', 'supportEmail', $all_params['supportEmail'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-2">
        <?=Html::label('vat')?>
        <?=Html::input('text', 'vat', $all_params['vat'], $options = ['class' => 'form-control'])?>
      </div>
      <div class="col-md-2">
        <?=Html::label('excise')?>
        <?=Html::input('text', 'excise', $all_params['excise'], $options = ['class' => 'form-control'])?>
      </div>

    </div>

    <div class="row m_top10">
      <div class="col-md-2">
        <?=Html::label('deviation(%)')?>
        <?=Html::input('text', 'deviation', $all_params['deviation'], $options = ['class' => 'form-control'])?>
      </div>
      <?
      $no_freeze = Yii::t('app', 'No freeze');
      $soat = Yii::t('app', 'Hour');
      $hours = [0 => $no_freeze, 1 => "1 ".$soat, 2 => "2 ".$soat, 3 => "3 ".$soat, 4 => "4 ".$soat];
      ?>
      <div class="col-md-2">
        <?=Html::label('plan_freeze_time')?>
        <?=Html::dropDownList('plan_freeze_time',
          $all_params['plan_freeze_time'],
          $hours,
          ['class' => 'form-control select2']
        )?>
      </div>
      <?
      $minut = Yii::t('app', 'Minut');
      $minuts = [0 => $no_freeze, 10 => "10 ".$minut, 15 => "15 ".$minut, 20 => "20 ".$minut, 30 => "30 ".$minut, 60 => "60 ".$minut, 120 => "120 ".$minut];
      ?>
      <div class="col-md-2">
        <?=Html::label('fact_freeze_time')?>
        <?=Html::dropDownList('fact_freeze_time',
          $all_params['fact_freeze_time'],
          $minuts,
          ['class' => 'form-control select2']
        )?>
      </div>

      <div class="col-md-2"></div>

      <div class="col-md-2">
        <?=Html::label('showKdCode')?><br>
        <label style="cursor:pointer; font-weight:normal"><input name="showKdCode" type="radio" value=1 <? if($all_params['showKdCode'] == true) echo "checked" ?> ><?=Yii::t('app', 'Yes')?>
        </label>
        <label style="cursor:pointer; font-weight:normal"><input name="showKdCode" type="radio" value=0 <? if($all_params['showKdCode'] == false) echo "checked" ?> ><?=Yii::t('app', 'No')?>
        </label>
      </div>
      <div class="col-md-2">
        <?=Html::label('ga2shop')?><br>
        <label style="cursor:pointer; font-weight:normal"><input name="ga2shop" type="radio" value=1 <? if($all_params['ga2shop'] == true) echo "checked" ?> ><?=Yii::t('app', 'Yes')?>
        </label>
        <label style="cursor:pointer; font-weight:normal"><input name="ga2shop" type="radio" value=0 <? if($all_params['ga2shop'] == false) echo "checked" ?> ><?=Yii::t('app', 'No')?>
        </label>
      </div>

    </div>

    <div class="row m_top10">
      <div class="col-md-6">
        <?=Html::label('uzAutoMotorsId')?>
        <?=Select2::widget(
          [
            'name' => 'uzAutoMotorsId',
            'value' => $all_params['uzAutoMotorsId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
      <div class="col-md-6">
        <?=Html::label('user_device_type')?>
        <?=Html::input('text', 'user_device_type', $all_params['user_device_type'], $options = ['class' => 'form-control'])?>
      </div>
    </div>

    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('inTransitWhId')?>
        <?=Select2::widget(
          [
            'name' => 'inTransitWhId',
            'value' => $all_params['inTransitWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>

    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('outsoursingWhId')?>
        <?=Select2::widget(
          [
            'name' => 'outsoursingWhId',
            'value' => $all_params['outsoursingWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('adjustmentWhId')?>
        <?=Select2::widget(
          [
            'name' => 'adjustmentWhId',
            'value' => $all_params['adjustmentWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('logxWhId, Regime 70')?>
        <?=Select2::widget(
          [
            'name' => 'logxWhId',
            'value' => $all_params['logxWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('kdWhId, Regime 40')?>
        <?=Select2::widget(
          [
            'name' => 'kdWhId',
            'value' => $all_params['kdWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('deliveryWhId')?>
        <?=Select2::widget(
          [
            'name' => 'deliveryWhId',
            'value' => $all_params['deliveryWhId'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>

    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('fg_wh_ids')?>
        <?=Select2::widget(
          [
            'name' => 'fg_wh_ids',
            'value' => $all_params['fg_wh_ids'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('damage_wh_ids')?>
        <?=Select2::widget(
          [
            'name' => 'damage_wh_ids',
            'value' => $all_params['damage_wh_ids'],
            'data' => $wh_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('import_contract_source_ids')?>
        <?=Select2::widget(
          [
            'name' => 'import_contract_source_ids',
            'value' => $all_params['import_contract_source_ids'],
            'data' => $c_s_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('local_contract_source_ids')?>
        <?=Select2::widget(
          [
            'name' => 'local_contract_source_ids',
            'value' => $all_params['local_contract_source_ids'],
            'data' => $c_s_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('cutted_coil_part_type_id')?>
        <?=Select2::widget(
          [
            'name' => 'cutted_coil_part_type_id',
            'value' => $all_params['cutted_coil_part_type_id'],
            'data' => $parttype_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>


    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('consignment_contract_source_ids')?>
        <?=Select2::widget(
          [
            'name' => 'consignment_contract_source_ids',
            'value' => $all_params['consignment_contract_source_ids'],
            'data' => $c_s_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>
    
    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('semi_contract_source_ids')?>
        <?=Select2::widget(
          [
            'name' => 'semi_contract_source_ids',
            'value' => $all_params['semi_contract_source_ids'],
            'data' => $c_s_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>

    <div class="row m_top10">
      <div class="col-lg-12">
        <?=Html::label('semi_production_line_ids')?>
        <?=Select2::widget(
          [
            'name' => 'semi_production_line_ids',
            'value' => $all_params['semi_production_line_ids'],
            'data' => $p_l_items,
            'maintainOrder' => true,
            'options' => ['placeholder' => '. . .', 'multiple' => true],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
          ]);
        ?>
      </div>
    </div>

    <? if(Yii::$app->user->identity->roleName == 'superadmin') { ?>
      <div class="row m_top10">
        <div class="col-md-4">
          <?=Html::label('less_dates_count')?>
          <?=Html::input('number', 'less_dates_count', $all_params['less_dates_count'], $options = ['class' => 'form-control', 'min' => 1])?>
        </div>
        <div class="col-md-4">
          <?=Html::label('greater_dates_count')?>
          <?=Html::input('number', 'greater_dates_count', $all_params['greater_dates_count'], $options = ['class' => 'form-control', 'min' => 1])?>
        </div>
        <div class="col-md-4">
          <?=Html::label('shipment_dates_count')?>
          <?=Html::input('number', 'shipment_dates_count', $all_params['shipment_dates_count'], $options = ['class' => 'form-control', 'min' => 1])?>
        </div>
      </div>
    <? } else { ?>
      <?=Html::hiddenInput('less_dates_count', $all_params['less_dates_count'])?>
      <?=Html::hiddenInput('greater_dates_count', $all_params['greater_dates_count'])?>
      <?=Html::hiddenInput('shipment_dates_count', $all_params['shipment_dates_count'])?>
    <? } ?>


    <br>
    <div class="form-group">
      <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
      <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
    </div>
    <?php ActiveForm::end(); ?>


  </div>

</div>
