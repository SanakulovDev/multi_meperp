<?php

use yii\helpers\Html;
?>
<div class="print-form">
    <form method="GET" action="/pechat-product/print-form?id=<?=$model->id?>" class="modalForm" id="PechatProduct">
        <div class="row">
            <input type="hidden" name="id" value="<?=$model->id?>">
            <div class="col-md-3">
                <div class="form-group field-pechatproduct-number_lot required">
                    <label class="control-label" for="pechatproduct-number">Количество</label>
                    <input type="text" id="pechatproduct-number" class="form-control" name="number" value='25'  min="1" max="25">

                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </form>
</div>