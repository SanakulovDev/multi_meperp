<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionRelease */

$this->title = Yii::t('app', 'Castle');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Castle'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
ob_start();

?>
    .header{
      height: 50px;
    }
    .bg-primaries{
      background-color: #9ccae8 !important;
		  font-weight: bold;
    }
    .bg-succesies{
      background-color: #19de83!important;
      color: white;
    }
    .bg-lighties{
        border: 1px solid black; 
		    background-color: #f2f2f2;
        font-weight: bold;
    } 
    .line {
      padding: 7px 45px;
      border: 2px solid black;
      font-weight: bold;
      cursor: pointer;  
    }
    th, td{
      text-align: left;
      text-transform: capitalize;
    }
    .left-border{
      border-left: none!important;
    }
    .right-border{
      border-right: none!important;
    }
    .center-border{
      border-left: none!important;
      border-right: none!important;
    }
    .top-border{
      border-top: none!important;
    }

    .bottom-border{
      border-bottom: none!important;
    }
    .wrapper-center-content{
      width: 90%;
    }
    .box1{
      height: 100px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border: 2px solid black;
      font-weight: bold;
      flex-direction: column;
    }
    .submit-btn{
      cursor: pointer;
    }
    label{
      float: left;
    }
    table input[type='checkbox']{
      width: 40px;
      height: 40px;
    }
    .wrapper-item{
      display: flex;
      align-items: center;
      justify-content: space-around;
    }
    table input[type="number"]{
      width: 100px;
    }
    h3{
      margin: 0;
      padding: 0;
    }

<?php $this->registerCss(ob_get_clean());?>
<div class="production-release-view">

    <div class="header">
      <div class="row">
        <div class="col-md-2 ">
          <span class="bg-primaries line">
            <?= Yii::t('app', 'Line')?>
          </span>
        </div>
        <?php foreach($lines as $key => $line):?>
          <?php $class ="";
              if($model->line == $key){
                $class = "bg-succesies";
              }
          ?>
          <div class="col-md-1 ">
            <span data-releaseId="<?=$releaseIds[$key-1]?>" class="bg-primaries  line <?=$class?>"><?= $key?></span>
          </div>
        <?php endforeach;?>
      </div>
    </div>
    <div class="wrapper-center-content">
      <div class="row">

        <div class="col-md-12">
          <table class="tbl_plan table table-bordered table1">
              <thead>
                  <tr>
                    <th class="bg-primaries bottom-border"><?= Yii::t('app', 'Production Order Number')?></th>
                    <th class="bg-primaries bottom-border"><?= Yii::t('app', 'Наименование')?></th>
                    <th class="bg-primaries bottom-border"><?= Yii::t('app', 'Quantity')?></th>
                    <th  class="right-border bg-primaries">начала</th>
                    <th  class="left-border bg-primaries"></th>

                    <th  class="right-border bg-primaries"></th>
                    <th  class="center-border bg-primaries"><?=Yii::t('app', 'Castle')?></th>
                    <th  class="left-border bg-primaries"></th>
                  </tr>
                  <tr>
                    <th  class="top-border right-border bg-primaries"></th>
                    <th  class="top-border right-border bg-primaries"></th>
                    <th  class="top-border right-border bg-primaries"></th>

                    <th  class="bg-primaries"><?=Yii::t('app', 'Date')?></th>
                    <th  class="bg-primaries"><?=Yii::t('app', 'Time')?></th>
                    
                    <th  class="bg-primaries"><?=Yii::t('app', 'Plan')?></th>
                    <th  class="bg-primaries"><?=Yii::t('app', 'Fakt')?></th>
                    <th  class="bg-primaries"><?=Yii::t('app', 'Balance')?></th>

                  </tr>
              </thead>  
              <tbody>
                <tr>
                  <td class="bg-lighties"><?= $model->pr_order_number?></td>
                  <td class="bg-lighties"><?= substr($model->part->part_no.$model->part->part_name, 0, 45)?></td>
                  <td class="bg-lighties"><?= divideString($model->quantity, 3)?></td>
                  <td class="bg-lighties"><?= date('d.m.Y', strtotime($model->target_date))?></td>
                  <td class="bg-lighties"><?= $model->time?></td>
                  <td class="bg-lighties"><?= divideString($model->planQty, 3)?>   (<?= $model->mixerPlan?>)</td>
                  <td class="bg-lighties"><?= divideString($model->fact*1, 3)?></td>
                  <td class="bg-lighties balance"><?= divideString(($model->planQty - $model->fact), 3)?></td>
                </tr>
              </tbody>
          </table>
          <br>
        </div>
        <!-- Data Siro -->
        <div class="col-md-10">
          <?php if(!empty($dataSiro)):?>
            <h3>Базовые сыря</h3>
            <table class="table table-bordered table2">
              <thead>
                <tr>
                  <th class="bg-primaries">№</th>
                  <th class="bg-primaries"><?= Yii::t('app', 'Calculation name')?></th>
                  <th class="bg-primaries"><?= Yii::t('app', 'Quantity')?></th>
                  <th class="bg-primaries"><?= Yii::t('app', 'Unit')?></th>
                  <th class="bg-primaries">Консентрация</th>
                  
                  
                </tr>
              </thead>
              <tbody>
                <?php $index = 0;?>
                <?php foreach($dataSiro as $item):?>
                    <?php
                      // $qty = 0;
                      // $qty = $item->usage_qty /  $mainProductSpecification->amount * $model->quantity;
                      // $protsent = 0;

                      // $protsent = $qty /  $model->quantity * 100;
                    ?>
                    <tr>
                      <td class="bg-lighties"><?= ++$index?></td>
                      <td class="bg-lighties"><?= $item['part_name']?></td>
                      <td class="bg-lighties"><?= divideString($item['main_qty'], 3)?></td>
                      <td class="bg-lighties"><?= $item['unit']?></td>
                      <td class="bg-lighties"><?= $item['protsent']?>%</td>
                    </tr>
                    
                <?php endforeach;?>
              </tbody>
            </table>
          <?php endif; ?>

        </div>
      </div>
      
          
         
        <!-- Data Zames -->
          <?php if(!empty($dataZames)):?>
            <?php 
              function findMiddleIndex($array) {
                $length = count($array);
                
                if ($length % 2 == 0) {
                    // Dizi uzunluğu çiftse
                    $middleIndex = $length / 2 - 1;
                } else {
                    // Dizi uzunluğu tekse
                    $middleIndex = floor($length / 2);
                }
            
                return $middleIndex;
            }
            
            // Test için örnek bir dizi
            $middleIndex = findMiddleIndex($dataZames);

              ?>
            <div class="row">
              <div class="col-md-10">
                <h3>Замес</h3>
                  <?php $form = ActiveForm::begin()?>
                    <table class="table table-bordered table2">
                      <thead>
                        <tr>
                          <th class="bg-primaries">№</th>
                          <th class="bg-primaries"><?= Yii::t('app', 'Calculation name')?></th>
                          <th class="bg-primaries"><?= Yii::t('app', 'Quantity')?></th>
                          <th class="bg-primaries"><?= Yii::t('app', 'Unit')?></th>
                          <th class="bg-primaries">Консентрация</th>
                          <th class="bg-primaries right-border"><?= Yii::t('app', 'Fakt')?></th>
                          <th class="bg-primaries left-border right-border"></th>
                          <th class="bg-primaries left-border right-border"></th>
                          <th class="bg-primaries"><?= Yii::t('app', 'remark')?></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $index = 0;?>
                        <?php foreach($dataZames as $key => $item):?>
                            
                            <tr>
                              <td class="bg-lighties">
                                <?= ++$index?>
                                <input type="hidden" value="<?=$id?>" name="ProductionReleaseItem[<?=$index-1?>][release_id]">
                            </td>
                              <td class="bg-lighties">
                                <?= $item['part_name']?>
                                <input type="hidden" value="<?=$item['part_id']?>" name="ProductionReleaseItem[<?=$index-1?>][partId]">
                            </td>
                              <td class="bg-lighties"><?= divideString($item['main_qty'], 3)?></td>
                              <td class="bg-lighties"><?= $item['unit']?></td>
                              <td class="bg-lighties"><?= $item['protsent']?>%</td>
                              <td class="bg-lighties">
                                <div class="wrapper-item">
                                  <label for="vehicle-<?=$index?>">OK</label>
                                  <input type="checkbox" data-id="<?=$index?>"  id="vehicle-<?=$index?>" value="<?= $item['status']?>"  class="form-checkbox" name="ProductionReleaseItem[<?=$index-1?>][status]">
                                </div>
                              </td>
                              <td class="bg-lighties">
                                  <!-- Umumiy table ni o'rtasiga  checkbox qo'yish kerak -->
                                  <?php if($middleIndex == $key):?>
                                    <div class="wrapper-item">
                                      <label for="general-checkbox">OK</label>
                                      <input type="checkbox" id="general-checkbox" name="general-checkbox">
                                    </div>
                                  <?php endif;?>
                              </td>
                              <td class="bg-lighties">
                                  <input type="number" class="fact-quantity form-control" data-id="<?=$index?>" value="<?= $item['qty']?>" name="ProductionReleaseItem[<?=$index-1?>][qty]" id="fact-input-<?=$index?>">
                                </td>
                                <td class="bg-lighties">
                                <input type="text" value="<?= $item['comment']?>" name="ProductionReleaseItem[<?=$index-1?>][comment]" class="form-control"  id="comment-input-<?=$index?>">

                              </td>
                              
                            </tr>
                            
                        <?php endforeach;?>
                      </tbody>
                    </table>
                    <button type="submit" class="submit-btn2 btn btn-success" style="float: right;">Сохранить</button>
                  <?php ActiveForm::end();?>
                </div>
                <div class="col-md-2">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="box1 bg-primaries">
                                  <span style="font-size: 45px;">№  <?= $id?></span>
                              </div>
                          </div>
                          <div class="col-md-12 " style="margin-top: 20px;">
                            <div class="box1 bg-primaries">
                              <span style="font-size: 20px;">Количество замесов</span>
                              <input type="number"  style="height: 40px;"class="form-control zamess-input" required>
                            </div>
                          </div>
                          <div class="col-md-12 " style="margin-top: 20px;">
                            <div class="box1 submit-btn bg-primaries">
                              <span  style="font-size: 45px;">OK</span>
                            </div>
                          </div>
                      </div>
                </div>
              </div>
                <!-- submit btn -->
                
            

          <?php endif; ?>


    </div>
</div>


<?php ob_start();?>
$(function(){

  $('body').on('click', '.submit-btn', function(){
    let fact = $('.zamess-input').val();
    if(fact == ''){
      $('.zamess-input').css('border', '1px solid red');
      // requiredv 
      $('.zamess-input').attr('required', true);
      alert('Заполните поле количество замесов');
      return false;
    }
    let params = {
      id: <?=$id?>,
      fact: fact
    }
    let url = '<?=Yii::$app->urlManager->createUrl(['production-release/add-fact'])?>';
    ajaxFunc(url, params, 'POST', function(res){
      if(res.status == 1){
        alert('Успешно добавлено');
        location.reload();
      }
      else{
        alert('Ошибка');
      }
    }) 
  })



  // form checkbox
  $('body').on('click', '.form-checkbox', function(){
    let id = $(this).data('id');
    let factQty = $('#fact-input-'+id).val();
    if(factQty != 0 &&  factQty != ''){
      $(this).prop('checked', false);
    }
    else{
      if ($(this).is(':checked')) {
        $('#fact-input-'+id).attr('readonly', true);
        $(this).val(1);
      }
      else{
        $(this).val(0);
        $('#fact-input-'+id).removeAttr('readonly');
      }
    }
    
    
  })


  $('.form-checkbox').each(function(){
    let id        = $(this).data('id');
    let variable  = $(this).val();
    let factQty = $('#fact-input-'+id).val();
    if(factQty != '' && factQty != '0'){
      $(this).prop('checked', false);
    }
    else{
      if(variable == 1){
        $('#fact-input-'+id).attr('readonly', true) ;
        let factQty = $('#fact-input-'+id).val();
        $(this).prop('checked', true);
      }
      else{
        $('#fact-input-'+id).removeAttr('readonly');
        $(this).removeAttr('checked');
      }
    }
    
  })

  // general checkbox
  $('body').on('click', '#general-checkbox', function(){
    if ($(this).is(':checked')) {
      $('.form-checkbox').each(function(){
        let id = $(this).data('id');
        let factQty = $('#fact-input-'+id).val();
        if(factQty != '' && factQty != '0'){
          $(this).prop('checked', false);
        }
        else{
          $(this).val(1);
          $('#fact-input-'+id).attr('readonly', true);
          //prop checked
          $(this).prop('checked', true);
        }
      })
    }
    else{
      $('.form-checkbox').each(function(){
        $(this).removeAttr('checked');
        $(this).prop('checked', false);
        $(this).val(0);
        let id = $(this).data('id');
        $('#fact-input-'+id).removeAttr('readonly');
      })
    }
  })


  // header qismidagi buttonlarni href qilish
  $('body').on('click', '.line', function(){
    let id = $(this).data('releaseid');
    if(id == 0){
      alert('Нет данных');
      return false;
    }
    window.location.href = '<?=Yii::$app->urlManager->createUrl(['production-release/view'])?>'+'?id='+id;
  })

  function factVallidate(id){
    let factQty = $('#fact-input-'+id).val();
    if(factQty != '' || factQty != 0){
      return true;
    }
    else{
      return false;
    }
  }
  function ajaxFunc(url, data, type, callback){
    $.ajax({
      url: url,
      data: data,
      type: type,
      success: function(res){
        callback(res);
      },
      error: function(err){
        console.log(err);
      }
    })
  }

})

<?php $this->registerJs(ob_get_clean());?>