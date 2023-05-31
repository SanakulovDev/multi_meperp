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
    .add-product-item, .submit-btn{
        background-color: #DDEBF6!important;
    }
    td, th {
        //border: 1px solid #000000!important;
        text-align:center;
    }
    .add-product-item, .submit-btn{
        border: 2px solid #000000!important;
        width: 100px;
        font-weight: bold;
    }
    .form-group{
        margin-bottom: 0px!important;
    }
    .bg-primaries{
        background-color: #DDEBF6!important;
        border: 2px solid black; margin: 5px 10px;
        padding: 5px 10px;
    }
    .bg-lighties{
        border: 1px solid black; 
        margin: 5px 10px;
        padding: 5px 10px;
    }
    .help-block{
        margin: 0px;
        padding: 0px;
    }
    .quantity, .part_id{
        border: 1px solid black!important;
        margin: 5px 0!important;
    }
    select.select2{
        margin: 5px 0!important;
    }
    span.select2{
        border: 1px solid black!important;
    }
    .form-group{
        margin: 5px 5px 0px;
    }
    .dashboard-row, .header-row{
        transform: translateX(95px);
    }
   
<?php  $content = ob_get_clean();?>
<?php $this->registerCss($content);?>
<div class="row  " style=" align-items:center; justify-content:center">

    <div class="col-md-10 header-row" style="border: 1.5px solid black; margin: 20px; padding: 20px;">  
    <h2 class="text-uppercase" style="font-weight: bold;">Availability Calculator</h2>
        <div class="col-md-8">

            <!-- activeform begin -->
            <?php $form = ActiveForm::begin(); ?>            
                <table class="table">
                    <thead>
                        <th>
                            <div class="bg-primaries">
                                №
                            </div>
                        </th>
                        <th>
                            <div class="bg-primaries">
                                Prooduct Name</th>
                            </div>
                        <th>
                            <div class="bg-primaries">
                                Quantity</th>
                            </div>    
                        </th>
                        <th></th>
                    </thead>
                    <tbody class="main-table-body">
                        <?php foreach($models as $key => $model): ?>
                            <tr class="item-<?=$key?>">
                                <td>
                                    <div class="bg-lighties">
                                        <?= $key+1 ?>
                                    </div>
                                </td>
                                <td>
                                    <!-- <div class="bg-lighties"> -->
                                        <?= $form->field($model, "[$key]part_id")->dropDownList($partlist, ['prompt' => '---', 'class' => 'select2 form-control part_id', 'data-id'=>$key])->label(false) ?>
                                    <!-- </div> -->
                                </td>
                                <td>
                                    <!-- <div class="bg-lighties"> -->
                                        <?= $form->field($model, "[$key]quantity")->textInput(['class' => 'form-control quantity text-right', 'data-id'=>$key, 'type'=>'number', 'placeholder'=>'0'])->label(false) ?>
                                    <!-- </div> -->
                                </td>

                                <td>
                                    <button class="btn btn-danger text-center remove-product-item " style="border: 2px solid black;" data-id="<?=$key?>"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="row">  
                    <div class="col-md-4">  
                        <button class="btn add-product-item" data-lastid="1" ><i class="fa fa-2x fa-plus"></i></button>
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
    </div>
   
</div>

<?php ob_start();?>
$(function(){
    //format number_format

    const format = (num, decimals) => num.toLocaleString('en-US', {
        minimumFractionDigits: 2,          
        maximumFractionDigits: 2,
    });
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
        let lastid = $('.add-product-item').data('lastid')-1;
        $('.add-product-item').data('lastid', lastid);
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