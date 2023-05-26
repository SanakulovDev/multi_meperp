<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CalculateProduct;
use app\models\PechatProduct;
use yii\helpers\ArrayHelper;
$list = [1,2,3,4,5];
$partlist = PechatProduct::getPartsList();
?>
<?php ob_start();?>

    td {
        margin:0;
        padding:0px!important;
        font-weight: bold;
        text-align: center;
    }
    table thead, .add-product-item, .submit-btn{
        background-color: #DDEBF6!important;
    }
    td, th {
        border: 2px solid #000000!important;
    }
    .add-product-item, .submit-btn{
        border: 2px solid #000000!important;
        width: 100px;
        font-weight: bold;
    }
    .form-group{
        margin-bottom: 0px!important;
    }
<?php  $content = ob_get_clean();?>
<?php $this->registerCss($content);?>
<div class="row">

    <div class="col-md-8">  
        <!-- activeform begin -->
        <?php $form = ActiveForm::begin(); ?>            
            <table class="table">
                <thead>
                    <th>№</th>
                    <th>Prooduct Name</th>
                    <th>Quantity</th>
                    <th>AVI</th>
                    <th>Balance</th>
                    <th></th>
                </thead>
                <tbody class="main-table-body">
                    <?php foreach($models as $key => $model): ?>
                        <tr class="item-<?=$key?>">
                            <td><?= $key+1 ?></td>
                            <td>
                                <?= $form->field($model, "[$key]part_id")->dropDownList($partlist, ['prompt' => '---', 'class' => 'select2 form-control part_id', 'data-id'=>$key])->label(false) ?>
                            </td>
                            <td class="">
                                <?= $form->field($model, "[$key]quantity")->textInput(['class' => 'form-control quantity text-right', 'data-id'=>$key, 'type'=>'number'])->label(false) ?>
                            </td>
                            <!-- norma rasxodada mavjud bo'lgan mahsulot og'irligi -->
                            <td class="avl-<?=$key?> calculate-avl" width="100px">
                                0
                            </td>
                            <td class="balance-<?=$key?> calculate-balance">
                                0
                            </td>

                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="row">  
                <div class="col-md-4">  
                    <button class="btn add-product-item" data-lastid="5" ><i class="fa fa-2x fa-plus"></i></button>
                    <button type="submit" class="btn submit-btn text-uppercase  btn-lg">ok</button>

                </div>
                <div class="col-md-8">
                    <div class="loader-ajax">
                        <!-- loadinf.gif -->
                        <img src="./img/loader.gif" class="hide loader-ajax" width="500px" style="margin:0; padding:0;" alt="">        
                    </div>

                </div>
            </div>
            <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
       
    </div>
</div>

<?php ob_start();?>
$(function(){
    $('.add-product-item').on('click', function(){
        let lastId = $(this).data('lastid');
        let url = '<?= Yii::$app->urlManager->createUrl(['calculate-product/new-product']) ?>';
        let param = {lastId: lastId};
        $(this).attr('disabled', true);
        let type = 'POST';
        let callback = function(data){
            $('.main-table-body').append(data);
            $('.select2').select2();
            $('.add-product-item').data('lastid', lastId+1);
            $('.add-product-item').attr('disabled', false);
        }
        ajaxxRequest(url, param, type, callback);
    });
    // body remove-product-item onclick
    $('body').on('click', '.remove-product-item', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        $('.item-'+id).remove();
        $('.add-product-item').data('lastid', id);
    });

    // dropdown onchange
    $('body').on('change', '.part_id', function(){
        let id = $(this).attr('data-id');
        let val = $(this).val();
        let url = '<?= Yii::$app->urlManager->createUrl(['calculate-product/get-product-ostatok']) ?>';
        let param = {
            'part_id': val,
        }
        let type = 'POST';
        let callback = function(data){
            let obj = JSON.parse(data);
            let quantity = $('#calculateproduct-'+id+'-quantity').val();
            let balance = obj-quantity;
            $('.avl-'+id).html(obj);
            $('.balance-'+id).html(balance);
            backBalance(balance, id);
        }
        ajaxxRequest(url, param, type, callback);
    });

    // quantity onchange
    $('body').on('keyup', '.quantity', function(){
        let id = $(this).attr('data-id');
        let val = $(this).val();
        let balance = $('.avl-'+id).html()-val;
        $('.balance-'+id).html(balance);
        backBalance(balance, id);
    });

   
    function ajaxxRequest(url, param, type, callback){
        $.ajax({
            url: url,
            type: type,
            data: param,
            success: function(data){
                callback(data);
            }
        })
    }
    function backBalance(qty, id){
        if(qty < 0){
            $('.balance-'+id).css('background-color', '#faa2a2');
        }
        else{
            $('.balance-'+id).css('background-color', 'lightgreen');
        }
    }
})


<?php $this->registerJs(ob_get_clean(), \yii\web\View::POS_READY); ?>