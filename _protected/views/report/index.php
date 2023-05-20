<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $reports */
$this->title = Yii::t('app', 'Reports');
$this->params['breadcrumbs'][] = $this->title;
$hiddenReports = [

  'vehicle-set',
  'pipeline',
  'import',
  'sp',
  'imos',
  'sum-imos',

  'pfep-parts',
  'doh-calc',

  'production-count-line',
  'ftq-by-line',
  'ftq-summary',

  'coverage-balance-old',
  'cash-requirement',

  'ccu',
  'ccum',
  
];
?>
<div class="report-index">
<style>
  

  .container-report {
    /* width: 1000px; */
    position: relative;
    display: flex;
    justify-content: space-around;
  }

  .container-report .card {
    position: relative;
    border-radius: 10px;
  }

  .container-report .card .icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: 0.7s;
    z-index: 1;
  }

 

 


  .container-report .card .icon .fa {
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 40px;
    transition: 0.7s;
    color: #fff;
  }

  .container-report>i {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 80px;
    transition: 0.7s;
    color: #fff;
  }

  .container-report .card .face {
    width: 400px;
    height: 300px;
    transition: 0.5s;
  }

  .container-report .card .face.face1 {
    position: relative;
    background: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1;
    transform: translateY(100px);
  }

  .container-report .card:hover .face.face1 {
    background: #ff0057;
    transform: translateY(0px);
  }

  .container-report .card .face.face1 .card-content {
    opacity: 1;
    transition: 0.5s;
  }

  .container-report .card:hover .face.face1 .card-content {
    opacity: 1;
  }

  .container-report .card .face.face1 .card-content i {
    max-width: 100px;
  }

  .container-report .card .face.face2 {
    position: relative;
    background: #fff;
    display: flex;
    /* justify-content: center; */
    /* align-items: center; */
    padding: 20px;
    box-sizing: border-box;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
    transform: translateY(-200px);
  }

  .container-report .card:hover .face.face2 {
    transform: translateY(0);
  }

  .container-report .card .face.face2 .card-content p {
    margin: 0;
    padding: 0;
    text-align: center;
    color: #414141;
  }

  .container-report .card .face.face2 .card-content h3 {
    margin: 0 0 10px 0;
    padding: 0;
    color: #fff;
    font-size: 24px;
    text-align: center;
    color: #414141;
  }

  .container-report a {
    text-decoration: none;
    color: #414141;
  }

  .card-title{
    font-size: 25px;
    text-align: center;
    color: #fff;
    margin-top: 60px;
  }

  .face ul {
    font-size: 16px;
    list-style: none;
    margin: 0px;
    padding: 0px;
  }

  .face ul li{
    border-left: 3px solid;
    margin-bottom: 5px;
    transition: .2s;
    width: 310px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .face ul li:hover{
    border-left: 3px solid #fff;
    transition: .2s;
  }

  .face ul li a{
    padding-left: 10px;
    transition: .2s;
  }

  .face ul li a:hover{
    padding-left: 20px;
    transition: .2s;
  }



  .card-blue .face .card-content .icon{
    background: #6eadd4;
  }
  .card-blue .face ul li{
    border-left-color: #6eadd4;
  }
  .card-blue .face ul li a:hover{
    color: #6eadd4;
  }

  .card-green .face .card-content .icon{
    background: #4aada9;
  }
  .card-green .face ul li{
    border-left-color: #4aada9;
  }
  .card-green .face ul li a:hover{
    color: #4aada9;
  }

  .card-ucell .face .card-content .icon{
    background: #9c27b0;
  }
  .card-ucell .face ul li{
    border-left-color: #9c27b0;
  }
  .card-ucell .face ul li a:hover{
    color: #9c27b0;
  }

  .card-orange .face .card-content .icon{
    background: #ff5722;
  }
  .card-orange .face ul li{
    border-left-color: #ff5722;
  }
  .card-orange .face ul li a:hover{
    color: #ff5722;
  }

  .card-green2 .face .card-content .icon{
    background: #4caf50;
  }
  .card-green2 .face ul li{
    border-left-color: #4caf50;
  }
  .card-green2 .face ul li a:hover{
    color: #4caf50;
  }


</style>
<div class="row">
  <div class="col-lg-10 col-lg-offset-1">
    <div class="container-report" style="margin-top: -50px;">

      <?
        foreach ($reportGroups as $key => $gr) { 
          if($key > 2) continue;
          if(count($gr->reports) == 0) continue;
          if($gr->id == 7) continue;
          
      ?>
        
      <div class="card" style="z-index: 1000;">
        <div class="face face1">
          <div class="card-content">
            <div class="icon" style="background: <?=$gr->color?>;">
              <p class="card-title"><?=Yii::t('app', $gr->name);?></p>
              <i class="<?=$gr->icon?>" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <?foreach ($gr->reports as $rp) {?>
                <? if(in_array($rp->action,$hiddenReports)) continue; ?>
                <li style="border-left-color: <?=$gr->color?>;">
                <a target="_blank" href="<?=Url::toRoute(['report/'.$rp->action])?>" onMouseOver="this.style.color='<?=$gr->color?>'" onMouseOut="this.style.color='#000'" title="<?=Yii::t('app',$rp->title)?>"><?=Yii::t('app',$rp->title)?></a></li>
              <?}?>
            </ul>
          </div>
        </div>

      <?}?>

      

    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-10 col-lg-offset-1">
    <div class="container-report" style="margin-top: -140px;">

    <?
        foreach ($reportGroups as $key => $gr) { 
          if($key <= 2) continue;
          if(count($gr->reports) == 0) continue;
          if($gr->id == 7) continue;

      ?>
        
        <div class="card">
        <div class="face face1">
          <div class="card-content">
            <div class="icon" style="background: <?=$gr->color?>;">
              <p class="card-title"><?=Yii::t('app', $gr->name);?></p>
              <i class="<?=$gr->icon?>" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <div class="face face2">
            <ul>
              <?foreach ($gr->reports as $rp) {?>
                <? if(in_array($rp->action,$hiddenReports)) continue; ?>
                <li style="border-left-color: <?=$gr->color?>;">
                <a target="_blank" href="<?=Url::toRoute(['report/'.$rp->action])?>" onMouseOver="this.style.color='<?=$gr->color?>'" onMouseOut="this.style.color='#000'" title="<?=Yii::t('app',$rp->title)?>"><?=Yii::t('app',$rp->title)?></a></li>
              <?}?>
            </ul>
          </div>
        </div>

      <?}?>

    </div>

  </div>
</div>




</div>
