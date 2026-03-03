<?php 

use yii\helpers\ArrayHelper;
use  yii\helpers\Html;
use yii\helpers\Url;
?>
<?php ob_start();?>
body{
    background-color: #000!important;
    font-size: 30px;
}
.row{
    display: flex;
    align-items: center;
    justify-content: center;
}
.header{
    margin-top: 20px;
    position:sticky;
    padding: 0 25px 30px 25px;
    z-index: 10000;
    background-color: #000;
    top:0px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.color-primary{
    color: #0BB5F3!important;
    font-weight: bold;
    font-size: 26px;
}
.color-success{
    color: #01AE53!important;
    font-weight: bold;
    font-size: 60px;
}
.color-danger{
    color: #FD0002!important;
    font-weight: bold;
    font-size: 60px;
}
.dropdown-toggle, .refresh{
    cursor: pointer;
}

.item-border-right{
    border-right: 4px solid #3A5424;
}
.item-quantity{
    margin: 25px;
    height: 160px;
    text-align: center;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
}
.item-quantity-title{
    font-size: 30px;
    transform: translateY(-20px);
}
.control-label{
    font-size: 14px;
}
.btn-light{
  background-color: grey;
  color: #222;
}
<?php $this->registerCss(ob_get_clean());?>
<div class="container-fluid" style="text-align:center">
    <div class="row">
      <div class="col-md-12" style="text-align: right">
          <?= Html::a(Yii::t('app', 'Logout'), ['/site/logout'], ['class'=> 'btn btn-light', 'data-method'=>'POST'])?>
      </div>
    </div>
    <div class="row header">
        <div class="col-md-5  text-right">
            <span class="color-primary"><?= Yii::t('app', 'Production result')?></span>
        </div>
        <div class="col-md-3  text-right">
            <!-- <span class="color-primary">Rezultat proizvodstva</span> -->
            <div class="btn-group">
                <span  class="dropdown-toggle color-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <?= \Yii::t('app', 'Line')?> 
                    <?php if(!empty($term)):?>
                        -<?= $term?>
                    <?php endif;?>
                </span>
                <ul class="dropdown-menu">
                    <?php foreach($lines as $key => $line):?>

                        <li><a href="javascript:void(0)" data-id="<?= $key?>" class="line"><?= $line?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <span class="color-primary time"></span>
        </div>
        <div class="col-md-2 text-left">
            <span class="color-primary refresh"><i class="fa fa-refresh fa-2x" style="transform:rotate(0deg);"></i></span>
        </div>
    </div>
    <div class="analiz"></div>
</div>


<!-- modal -->
<div class="modal fade " tabindex="-1" role="dialog" style="z-index:1111111111111111111111;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="display: inline-block;float: left;"><?= Yii::t('app', 'Production count')?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app', 'btn-cancel')?></button>
          <button type="button" class="btn btn-success modalFormSubmit1"><?= Yii::t('app', 'btn-save')?></button>
      </div>
    </div>
  </div>
</div>
<?php ob_start();?>
$(function(){
    let deg  = 0;
    let analizUrl = '<?= Url::to(['dashboard/analiz-ajax'])?>';
    let param = {
        line: '<?= $term?>'
    };
    ajaxFunc(analizUrl, param, 'POST', function(data){
        let response = JSON.parse(data);
        $('.analiz').html(response.html);
        $('.time').html(response.nowTime);
    });
    $('.refresh').on('click', function(e){
        deg += 720;
        $(this).find('i').css({
            'transform': 'rotate('+deg+'deg)',
            'transition': 'transform 1s ease-in-out'
        });
        ajaxFunc(analizUrl, param, 'POST', function(data){
            let response = JSON.parse(data);
            $('.analiz').html(response.html);
            $('.time').html(response.nowTime);
        });
    });


    //line

    $('.line').on('click', function(){
        let id = $(this).data('id');
        window.location.href = '<?= Url::to(['dashboard/analiz'])?>?line='+id+'&status=1';
    })

    function ajaxFunc(url, param, type, callback){
        $.ajax({
            url: url,
            type: type,
            data: param,
            success: function(data){
                callback(data);
            }
        })
    }


    

    // modal show_source

    $('body').on('click', '.form-modal', function(){
        let part_id = $(this).data('partid');
        let href = $(this).data('href');
        let line = $(this).data('line');
        let shift = $(this).data('shift');  
        let wrapper_code    = $(this).data('wrapper-code');  
        let wrapper_id      = $(this).data('wrapper-id');  
        
        let param = {
            part_id: part_id,
            line: line,
            shift: shift,
            wrapper_code: wrapper_code,
            wrapper_id: wrapper_id,
        };
        let url = '<?= Url::to(['dashboard/analiz-form-modal'])?>';
        $.get(url, param, function(data){
            $('.modal-body').html(data);
            $('.modal').modal('show');
        })
    })

    $('body').on('click', '.modalFormSubmit1', function(e){
        e.preventDefault();
        let form = $('.modal-body').find('form');
        let url = form.attr('action');
        let data = form.serialize();
        console.log(data);
        ajaxFunc(url, data, 'POST', function(data){
            if(data.status == 1){
                $('.modal').modal('hide');
                $('.refresh').trigger('click');
            }
            else{
              $('.modal').find('.error-alert').html(data.message);
              //$('.modal').modal('hide');
              //$('.refresh').trigger('click');
              //alert('Xatolik sodir boldi');
            }
        })
    })

    // har 5 soniyada liniyalar almashsin
    if(<?= $status?> ==  0){
       setInterval(function(){
        let line = '<?=$term?>';
        if(line == 7){
          line = 0;
        }
        else{
          line++;
        }
        window.location.href = '<?= Url::to(['dashboard/analiz'])?>?line='+line;
      }, 5000);
    }
   
})

<?php $this->registerJs(ob_get_clean());?>
