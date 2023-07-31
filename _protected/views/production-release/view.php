<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
    .header .line{
      padding: 7px 45px;
      border: 2px solid black;
      font-weight: bold;
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
      justify-content: center;
      border: 2px solid black;
      font-weight: bold;
      flex-direction: column;
    }
    .submit-btn{
      cursor: pointer;
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
        <?php foreach($lines as $line):?>
          <?php $class ="";
              $line = preg_replace('/[^0-9]/', '', $line);
              if($model->line == $line){
                $class = "bg-succesies";
              }
          ?>
          <div class="col-md-1 ">
            <span class="bg-primaries  line <?=$class?>"><?= $line?></span>
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
                  <td class="bg-lighties"><?= divideString($model->powerPlan?$model->powerPlan->special:0 , 3)?></td>
                  <td class="bg-lighties"><?= divideString($model->fact*1, 3)?></td>
                  <td class="bg-lighties"><?= divideString((($model->powerPlan?$model->powerPlan->special:0) - $model->fact), 3)?></td>
                </tr>
              </tbody>
          </table>
          <br>
        </div>
        <div class="col-md-10">
          <?php if(!empty($model2)):?>
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
                <?php foreach($model2 as $item):?>
                  <?php if($item->part  && $item->part->state == 2):?>
                    <?php
                      $qty = 0;
                      $qty = $item->usage_qty /  $mainProductSpecification->amount * $model->quantity;
                      $protsent = 0;

                      $protsent = $item->usage_qty /  $model->quantity * 100;
                    ?>
                    <tr>
                      <td class="bg-lighties"><?= ++$index?></td>
                      <td class="bg-lighties"><?= $item->part->part_name?></td>
                      <td class="bg-lighties"><?= divideString(round($qty*1), 3)?></td>
                      <td class="bg-lighties"><?= $item->part->unit->unit_value?></td>
                      <td class="bg-lighties"><?= $protsent?>%</td>
                    </tr>
                    
                  <?php endif;?>
                <?php endforeach;?>
              </tbody>
            </table>
          <?php endif; ?>

        </div>
      </div>
      
          
         

          <?php if(!empty($model2)):?>
            <div class="row">
              <div class="col-md-10">
                <table class="table table-bordered table2">
                  <thead>
                    <tr>
                      <th class="bg-primaries">№</th>
                      <th class="bg-primaries"><?= Yii::t('app', 'Calculation name')?></th>
                      <th class="bg-primaries"><?= Yii::t('app', 'Quantity')?></th>
                      <th class="bg-primaries"><?= Yii::t('app', 'Unit')?></th>
                      <th class="bg-primaries">Консентрация</th>
                      <th class="bg-primaries right-border"><?= Yii::t('app', 'Fakt')?></th>
                      <th class="bg-primaries left-border"></th>
                      <th class="bg-primaries"><?= Yii::t('app', 'remark')?></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $index = 0;?>
                    <?php foreach($model2 as $item):?>
                      <?php if($item->part  && $item->part->state !== 2):?>
                        <?php
                          $qty = 0;
                          $qty = $item->usage_qty /  $mainProductSpecification->amount * $model->quantity;
                          $protsent = 0;

                          $protsent = $item->usage_qty /  $model->quantity * 100;
                        ?>
                        <tr>
                          <td class="bg-lighties"><?= ++$index?></td>
                          <td class="bg-lighties"><?= $item->part->part_name?></td>
                          <td class="bg-lighties"><?= divideString(round($qty*1, 2), 3)?></td>
                          <td class="bg-lighties"><?= $item->part->unit->unit_value?></td>
                          <td class="bg-lighties"><?= $protsent?>%</td>
                          <td class="bg-lighties">
                            <label for="vehicle-<?=$index?>">OK</label>
                            <input type="checkbox" id="vehicle-<?=$index?>" name="vehicle1" value="<?=$index?>">
                          </td>
                          <td class="bg-lighties">
                              
                          </td>
                          <td class="bg-lighties">

                          </td>
                          
                        </tr>
                        
                      <?php endif;?>
                    <?php endforeach;?>
                  </tbody>
                </table>
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
                            <span style="font-size: 25px;">Количество замесов</span>
                            <input type="number" class="form-control zamess-input" required>
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